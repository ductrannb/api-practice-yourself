<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RequestForgetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendMailForgetPassword;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Utils\Messages;
use Illuminate\Http\Response;
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

    public function register(RegisterRequest $request)
    {
        return $this->createdSuccess(data: new UserResource($this->userRepository->create($request->validated())));
    }

    public function requestForgetPassword(RequestForgetPasswordRequest $request)
    {
        $user = $this->userRepository->firstOfWhere(['email' => $request->email]);
        dispatch(new SendMailForgetPassword($user, $this->getOtp()));
        return $this->responseOk(Messages::OTP_SEND_MESSAGE);
    }

    public function getOtp() : int
    {
        return rand(100000, 999999);
    }

    /**
     * @throws ValidationException
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $verifyOtp = $this->otpRepository->latestOfWhere(['email' => $request->email, 'is_used' => false]);
        if (!$verifyOtp) {
            throw ValidationException::withMessages(['otp' => Messages::OTP_INVALID_MESSAGE]);
        }
        if ($verifyOtp->count_wrong >= self::MAX_COUNT_WRONG || $verifyOtp->expired_at < now()) {
            return $this->responseError(Messages::OTP_TIMEOUT_MESSAGE, errorCode: Response::HTTP_REQUEST_TIMEOUT);
        }
        if ($verifyOtp->code != $request->otp) {
            $verifyOtp->count_wrong ++;
            $verifyOtp->save();
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
        return $this->responseOk('Successfully logged out');
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
