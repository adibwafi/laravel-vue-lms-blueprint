<?php

namespace App\Http\Controllers;

use Ferdous\OtpValidator\Object\OtpRequestObject;
use Ferdous\OtpValidator\OtpValidator;
use Ferdous\OtpValidator\Object\OtpValidateRequestObject;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    /**
     * @return array
     */
    public function requestForOtp()
    {
        return OtpValidator::requestOtp(
            new OtpRequestObject('123', 'verify', '123', 'fowore9144@nicoimg.com')
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function validateOtp(Request $request)
    {
        $uniqId = $request->input('uniqueId');
        $otp = $request->input('otp');
        return OtpValidator::validateOtp(
            new OtpValidateRequestObject($uniqId, $otp)
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    public function resendOtp(Request $request)
    {
        $uniqueId = $request->input('uniqueId');
        return OtpValidator::resendOtp($uniqueId);
    }
}
