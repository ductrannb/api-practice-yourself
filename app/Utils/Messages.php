<?php

namespace App\Utils;

class Messages
{
    const UNAUTHORIZED_MESSAGE = 'Unauthorized';

    const OTP_SEND_MESSAGE = 'Mã OTP đã được gửi tới email của bạn.';
    const OTP_INVALID_MESSAGE = 'Mã OTP không chính xác.';
    const OTP_TIMEOUT_MESSAGE = 'Mã OTP đã hết hạn hoặc nhập sai quá nhiều lần.';

    const UPDATE_PASSWORD_SUCCESS_MESSAGE = 'Cập nhật mật khẩu mới thành công.';
    const PASSWORD_INVALID_MESSAGE = 'Mật khẩu không chính xác.';
    const REGISTER_SUCCESS_MESSAGE = 'Đăng ký thành công.';
    const LOGOUT_SUCCESS_MESSAGE = 'Đăng xuất thành công.';

    const CREATE_SUCCESS_MESSAGE = 'Tạo thành công.';
    const UPDATE_SUCCESS_MESSAGE = 'Cập nhật thành công.';
    const DELETE_SUCCESS_MESSAGE = 'Xóa thành công.';

    const REGISTER_COURSE_SUCCESS = 'Đăng ký khóa học thành công.';
    const REGISTER_COURSE_EXISTED = 'Bạn đã đăng ký khóa học này rồi.';
    const REGISTER_COURSE_NOT_ENOUGH_MONEY = 'Số dư của bạn không đủ.';
    const REGISTER_COURSE_NOT_EXIST = 'Bạn chưa đăng ký khóa học này.';

    const EXAM_IS_EMPTY = 'Bài thi thử không có câu hỏi nào.';
    const EXAM_SUBMIT_SUCCESS = 'Nộp bài thi thử thành công.';
}
