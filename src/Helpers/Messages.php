<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Constants for error and success messages.
 */
class Messages
{
    // Auth errors
    /** @var string Email is required error */
    public const EMAIL_REQUIRED = 'Укажите корректный email.';
    /** @var string Email already exists error */
    public const EMAIL_EXISTS = 'Этот email уже зарегистрирован.';
    /** @var string Email and password required error */
    public const EMAIL_PASSWORD_REQUIRED = 'Введите email и пароль.';
    public const INVALID_CREDENTIALS = 'Неверный email или пароль.';
    public const PASSWORD_MIN_LENGTH = 'Пароль должен быть не короче 8 символов.';
    public const PASSWORD_MISMATCH = 'Пароль и подтверждение не совпадают.';

    // Name validation
    public const FIRST_NAME_INVALID = 'Имя должно содержать только буквы (пробел и дефис допускаются).';
    public const LAST_NAME_INVALID = 'Фамилия должна содержать только буквы (пробел и дефис допускаются).';

    // Date of birth
    public const DOB_FORMAT = 'Дата рождения должна быть в формате YYYY-MM-DD.';
    public const DOB_AGE_LIMIT = 'Регистрация доступна с 18 лет.';

    // Phone
    public const PHONE_INVALID = 'Телефон должен быть в формате +374 00 000 000.';

    // Topic errors
    public const TOPIC_TITLE_MIN = 'Заголовок должен быть не короче 3 символов.';
    public const TOPIC_BODY_MIN = 'Текст темы должен быть не короче 10 символов.';
    public const TOPIC_NOT_FOUND = 'Тема не найдена.';
    public const TOPIC_FORBIDDEN = 'У вас нет прав на редактирование этой темы.';

    // Comment errors
    public const COMMENT_BODY_MIN = 'Комментарий слишком короткий.';
    public const COMMENT_NOT_FOUND = 'Комментарий не найден.';
    public const COMMENT_FORBIDDEN = 'У вас нет прав на редактирование этого комментария.';

    // Success messages
    public const REGISTER_SUCCESS = 'Регистрация успешна. Теперь войдите.';
    public const LOGIN_SUCCESS = 'Вы вошли в систему.';
    public const LOGOUT_SUCCESS = 'Вы вышли из системы.';
    public const TOPIC_CREATED = 'Тема создана.';
    public const TOPIC_UPDATED = 'Тема обновлена.';
    public const TOPIC_DELETED = 'Тема удалена.';
    public const COMMENT_ADDED = 'Комментарий добавлен.';
    public const COMMENT_UPDATED = 'Комментарий обновлён.';
    public const COMMENT_DELETED = 'Комментарий удалён.';
    public const PROFILE_UPDATED = 'Профиль обновлён.';

    // Auth required
    public const AUTH_REQUIRED = 'Войдите, чтобы продолжить.';

    // Rate limiting
    public const RATE_LIMITED = 'Слишком много попыток. Попробуйте позже.';

    // General
    public const NOT_FOUND = 'Страница не найдена.';
    public const GENERAL_ERROR = 'Произошла ошибка. Попробуйте ещё раз.';
}
