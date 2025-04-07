import {  HTTP_CODE_UNAUTHORIZED, HTTP_CODE_NOT_FOUND, HTTP_CODE_INVALID_DATA, HTTP_SERVER_ERROR } from './constants.js'

function formatErrorResponse(error) {

    if (error.status >= HTTP_SERVER_ERROR) {
        return 'errors.system_error'
    }

    if (error.status == HTTP_CODE_INVALID_DATA) {
        return 'errors.invalid_data'
    }

    if (error.status == HTTP_CODE_NOT_FOUND) {
        return 'errors.non_exisitng_data'
    }

    if (error.status == HTTP_CODE_UNAUTHORIZED) {
        return 'errors.unauthorized'
    }

    if (error.response == null || error.response.data == null ) {
        return 'errors.system_error'
    }

    const data = error.response.data;

    if (data.errors != null && Array.isArray(data.errors) && data.errors.length > 0 && data.errors[0].startsWith('APP_ERROR_')) {
        
        return data.errors[0].replace('APP_ERROR_', 'errors.').toLowerCase();
    }

    return data.message != null ? data.message : 'errors.system_error'
}

export {formatErrorResponse}