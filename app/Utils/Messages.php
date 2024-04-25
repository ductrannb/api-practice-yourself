<?php

namespace App\Utils;

class Messages
{
    const OTP_SEND_MESSAGE = 'Mã OTP đã được gửi tới email của bạn.';
    const OTP_INVALID_MESSAGE = 'Mã OTP không chính xác.';
    const OTP_TIMEOUT_MESSAGE = 'Mã OTP đã hết hạn hoặc nhập sai quá nhiều lần.';

    const UPDATE_PASSWORD_SUCCESS_MESSAGE = 'Cập nhật mật khẩu mới thành công.';
    const PASSWORD_INVALID_MESSAGE = 'Mật khẩu không chính xác';
    const REGISTER_SUCCESS_MESSAGE = 'Đăng ký thành công';

    const CREATE_SUCCESS_MESSAGE = 'Tạo thành công';
    const UPDATE_SUCCESS_MESSAGE = 'Cập nhật thành công';
    const DELETE_SUCCESS_MESSAGE = 'Xóa thành công';
}
