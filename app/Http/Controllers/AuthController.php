<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendMailForgetPassword;
use App\Models\Otp;
use App\Models\User;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Utils\Messages;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const REFRESH_TOKEN_EXPIRED = 4320; //minutes
    private const MAX_COUNT_WRONG = 10;
    private $userRepository;
    private $otpRepository;

    public function __construct(UserRepository $userRepository, OtpRepository $otpRepository)
    {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
    }

    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        if (!$token) {
            return $this->responseUnauthorized();
        }
        return $this->respondWithToken($token);
    }

    /**
     * @throws Exception
     */
    public function register(RegisterRequest $request)
    {
        if (!$this->verifyOtp($request->email, $request->otp)) {
            throw ValidationException::withMessages(['otp' => Messages::OTP_INVALID_MESSAGE]);
        }
        $user = $this->userRepository->create(Arr::except($request->validated(), ['otp']));
        return $this->createdSuccess(Messages::REGISTER_SUCCESS_MESSAGE);
    }

    public function sendOtp(SendOtpRequest $request)
    {
        $user = $this->userRepository->firstOfWhere(['email' => $request->email]);
        $otp = rand(100000, 999999);
        if (!$user) {
            $user = (object)['email' => $request->email, 'name' => 'bạn'];
        }
        dispatch(new SendMailForgetPassword($user, $otp));
        return $this->responseOk(Messages::OTP_SEND_MESSAGE);
    }

    /**
     * @throws Exception
     */
    public static function verifyOtp($email, $otp) : bool
    {
        $verifyOtp = Otp::where(['email' => $email, 'is_used' => false])->latest()->first();
        if (!$verifyOtp) {
            return false;
        }
        if ($verifyOtp->count_wrong >= self::MAX_COUNT_WRONG || $verifyOtp->expired_at < now()) {
            throw new Exception(Messages::OTP_TIMEOUT_MESSAGE, Response::HTTP_REQUEST_TIMEOUT);
        }
        if ($verifyOtp->code != $otp) {
            $verifyOtp->count_wrong ++;
            $verifyOtp->save();
            return false;
        }
        $verifyOtp->is_used = true;
        $verifyOtp->save();
        return true;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        if (!$this->verifyOtp($request->email, $request->otp)) {
            throw ValidationException::withMessages(['otp' => Messages::OTP_INVALID_MESSAGE]);
        }
        $this->userRepository->updateWithConditions(['email' => $request->email], ['password' => $request->new_password]);
        return $this->responseOk(Messages::UPDATE_PASSWORD_SUCCESS_MESSAGE);
    }

    /**
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $checkPassword = Hash::check($request->password, auth()->user()->password);
        if (!$checkPassword) {
            throw ValidationException::withMessages(['password' => Messages::PASSWORD_INVALID_MESSAGE]);
        }
        $this->userRepository->update(auth()->id(), ['password' => $request->new_password]);
        return $this->responseOk(Messages::UPDATE_PASSWORD_SUCCESS_MESSAGE);
    }

    public function me()
    {
        return $this->responseOk(data: new UserResource(auth()->user()));
    }

    public function logout()
    {
        auth()->logout();
        return $this->responseOk(Messages::LOGOUT_SUCCESS_MESSAG);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
