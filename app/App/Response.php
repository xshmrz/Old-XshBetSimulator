<?php
    use Illuminate\Http\JsonResponse;
    function responseContinue($response = [], $status = 100) {
        return new JsonResponse($response, $status);
    }
    function responseSwitchingProtocols($response = [], $status = 101) {
        return new JsonResponse($response, $status);
    }
    function responseProcessing($response = [], $status = 102) {
        return new JsonResponse($response, $status);
    }
    function responseEarlyHints($response = [], $status = 103) {
        return new JsonResponse($response, $status);
    }
    function responseOk($response = [], $status = 200) {
        return new JsonResponse($response, $status);
    }
    function responseCreated($response = [], $status = 201) {
        return new JsonResponse($response, $status);
    }
    function responseAccepted($response = [], $status = 202) {
        return new JsonResponse($response, $status);
    }
    function responseNonAuthoritativeInformation($response = [], $status = 203) {
        return new JsonResponse($response, $status);
    }
    function responseNoContent($response = [], $status = 204) {
        return new JsonResponse($response, $status);
    }
    function responseResetContent($response = [], $status = 205) {
        return new JsonResponse($response, $status);
    }
    function responsePartialContent($response = [], $status = 206) {
        return new JsonResponse($response, $status);
    }
    function responseMultiStatus($response = [], $status = 207) {
        return new JsonResponse($response, $status);
    }
    function responseAlreadyReported($response = [], $status = 208) {
        return new JsonResponse($response, $status);
    }
    function responseIAmUsed($response = [], $status = 226) {
        return new JsonResponse($response, $status);
    }
    function responseMultipleChoices($response = [], $status = 300) {
        return new JsonResponse($response, $status);
    }
    function responseMovedPermanently($response = [], $status = 301) {
        return new JsonResponse($response, $status);
    }
    function responseFound($response = [], $status = 302) {
        return new JsonResponse($response, $status);
    }
    function responseSeeOther($response = [], $status = 303) {
        return new JsonResponse($response, $status);
    }
    function responseNotModified($response = [], $status = 304) {
        return new JsonResponse($response, $status);
    }
    function responseUseProxy($response = [], $status = 305) {
        return new JsonResponse($response, $status);
    }
    function responseSwitchProxy($response = [], $status = 306) {
        return new JsonResponse($response, $status);
    }
    function responseTemporaryRedirect($response = [], $status = 307) {
        return new JsonResponse($response, $status);
    }
    function responsePermanentRedirect($response = [], $status = 308) {
        return new JsonResponse($response, $status);
    }
    function responseBadRequest($response = [], $status = 400) {
        return new JsonResponse($response, $status);
    }
    function responseUnauthorized($response = [], $status = 401) {
        return new JsonResponse($response, $status);
    }
    function responsePaymentRequired($response = [], $status = 402) {
        return new JsonResponse($response, $status);
    }
    function responseForbidden($response = [], $status = 403) {
        return new JsonResponse($response, $status);
    }
    function responseNotFound($response = [], $status = 404) {
        return new JsonResponse($response, $status);
    }
    function responseMethodNotAllowed($response = [], $status = 405) {
        return new JsonResponse($response, $status);
    }
    function responseNotAcceptable($response = [], $status = 406) {
        return new JsonResponse($response, $status);
    }
    function responseProxyAuthenticationRequired($response = [], $status = 407) {
        return new JsonResponse($response, $status);
    }
    function responseRequestTimeout($response = [], $status = 408) {
        return new JsonResponse($response, $status);
    }
    function responseConflict($response = [], $status = 409) {
        return new JsonResponse($response, $status);
    }
    function responseGone($response = [], $status = 410) {
        return new JsonResponse($response, $status);
    }
    function responseLengthRequired($response = [], $status = 411) {
        return new JsonResponse($response, $status);
    }
    function responsePreconditionFailed($response = [], $status = 412) {
        return new JsonResponse($response, $status);
    }
    function responsePayloadTooLarge($response = [], $status = 413) {
        return new JsonResponse($response, $status);
    }
    function responseUriTooLong($response = [], $status = 414) {
        return new JsonResponse($response, $status);
    }
    function responseUnsupportedMediaType($response = [], $status = 415) {
        return new JsonResponse($response, $status);
    }
    function responseRangeNotSatisfiable($response = [], $status = 416) {
        return new JsonResponse($response, $status);
    }
    function responseExpectationFailed($response = [], $status = 417) {
        return new JsonResponse($response, $status);
    }
    function responseIAmATeapot($response = [], $status = 418) {
        return new JsonResponse($response, $status);
    }
    function responseMisdirectedRequest($response = [], $status = 421) {
        return new JsonResponse($response, $status);
    }
    function responseUnprocessableEntity($response = [], $status = 422) {
        return new JsonResponse($response, $status);
    }
    function responseLocked($response = [], $status = 423) {
        return new JsonResponse($response, $status);
    }
    function responseFailedDependency($response = [], $status = 424) {
        return new JsonResponse($response, $status);
    }
    function responseTooEarly($response = [], $status = 425) {
        return new JsonResponse($response, $status);
    }
    function responseUpgradeRequired($response = [], $status = 426) {
        return new JsonResponse($response, $status);
    }
    function responsePreconditionRequired($response = [], $status = 428) {
        return new JsonResponse($response, $status);
    }
    function responseTooManyRequests($response = [], $status = 429) {
        return new JsonResponse($response, $status);
    }
    function responseRequestHeaderFieldsTooLarge($response = [], $status = 431) {
        return new JsonResponse($response, $status);
    }
    function responseUnavailableForLegalReasons($response = [], $status = 451) {
        return new JsonResponse($response, $status);
    }
    function responseInternalServerError($response = [], $status = 500) {
        return new JsonResponse($response, $status);
    }
    function responseNotImplemented($response = [], $status = 501) {
        return new JsonResponse($response, $status);
    }
    function responseBadGateway($response = [], $status = 502) {
        return new JsonResponse($response, $status);
    }
    function responseServiceUnavailable($response = [], $status = 503) {
        return new JsonResponse($response, $status);
    }
    function responseGatewayTimeout($response = [], $status = 504) {
        return new JsonResponse($response, $status);
    }
    function responseHttpVersionNotSupported($response = [], $status = 505) {
        return new JsonResponse($response, $status);
    }
    function responseVariantAlsoNegotiates($response = [], $status = 506) {
        return new JsonResponse($response, $status);
    }
    function responseInsufficientStorage($response = [], $status = 507) {
        return new JsonResponse($response, $status);
    }
    function responseLoopDetected($response = [], $status = 508) {
        return new JsonResponse($response, $status);
    }
    function responseNotExtended($response = [], $status = 510) {
        return new JsonResponse($response, $status);
    }
    function responseNetworkAuthenticationRequired($response = [], $status = 511) {
        return new JsonResponse($response, $status);
    }
