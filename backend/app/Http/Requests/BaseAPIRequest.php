<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseAPIRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->all(), $this->query());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Exception
     */
    public function rules()
    {
        switch ($this->getMethod()) {
            case 'GET':
                return $this->rulesGet();
            case 'POST':
                return $this->rulesPost();
            case 'PUT':
                return $this->rulesPut();
            default:
                throw new \Exception('Not define');
        }
    }

    /**
     * Custom errors response
     * Error 422 : When the validator return fail
     * Error 409 : The HTTP request was valid, but the current state of the server prevents it from being executed.
     *
     * @param Validator $validator : validator
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $errorsList = $validator->errors()->getMessages();

        $errorReturn = [];
        $responseCode = 422;
        foreach ($errorsList as $key => $errorList) {
            foreach ($errorList as $error) {
                if ($error == 'conflict') {
                    $responseCode = 409;
                }

                $errorContent = [
                    'field' => $key,
                    'errorCode' => $error,
                    'errorMessage' => __('validation.errorCode.' . $error),
                ];
                array_push($errorReturn, $errorContent);
            }
        }

        \Log::error($validator->errors());

        throw new HttpResponseException(response()->json([
            'status' => 'failure',
            'message' => config('const.httpStatusCode.' . $responseCode . '.message'),
            'result' => $errorReturn,
        ], $responseCode));
    }

    /**
     * rulesGet
     * handle rule method get
     *
     * @return array
     */
    public function rulesGet()
    {
        return [];
    }

    /**
     * rulesPost
     * handle rule method post
     *
     * @return array
     */
    public function rulesPost()
    {
        return [];
    }

    /**
     * rulesPut
     * handle rule method put
     *
     * @return array
     */
    public function rulesPut()
    {
        return [];
    }
}
