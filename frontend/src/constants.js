const HTTP_CODE_SUCCESS = 200
const HTTP_CODE_UNAUTHORIZED = 401

const URL_CSRF_COOKIE = '/sanctum/csrf-cookie'

const URL_REGISTER = '/auth/register'
const URL_LOGIN = '/auth/login'
const URL_LOGOUT = '/auth/logout'

const URL_GET_USER = '/api/user'

const URL_GET_PURCHASE_ITEMS = '/api/purchase_items'

const URL_EXPORT_JSON = '/api/purchase_list'
const URL_IMPORT_JSON = URL_EXPORT_JSON

export {
  HTTP_CODE_SUCCESS,
  HTTP_CODE_UNAUTHORIZED,
  URL_CSRF_COOKIE,
  URL_REGISTER,
  URL_LOGIN,
  URL_LOGOUT,
  URL_GET_USER,
  URL_GET_PURCHASE_ITEMS,
  URL_EXPORT_JSON,
  URL_IMPORT_JSON,
}
