<?php

header('Content-type: application/json');
$reqjson = json_decode(file_get_contents("php://input"), true);

abstract class branchName
{
    const DoNotBegin = 0;
    const PromptAndCollectNextResponse = 1;
    const ReturnControlToScript = 2;
    const EndContact = 3;
    const AudioInputUntranscribeable = 4;
    const Error = 5;
    const DTMFBreakout = 6;
    const UserInputTimeout = 7;
    const UserInputNotUnderstood = 8;
}

abstract class userInputType
{
    const NO_INPUT = 0;
    const TEXT = 1;
    const BASE64_ENCODED_G711_ULAW_WAV_FILE = 2;
    const USER_INPUT_ARCHIVED_AS_SPECIFIED = 3;
    const USER_ENDED_SESSION = 4;
    const AUTOMATED_TEXT = 5;
    const DTMF_AS_TEXT = 6;
}

$botExchangeResponse['branchName'] = branchName::PromptAndCollectNextResponse;

function setIntent($intent, $intentConfidence)
{
    $intentInfo['intent'] = $intent;
    $intentInfo['intentConfidence'] = $intentConfidence;
    return $intentInfo;
}

if ($reqjson['userInputType'] === userInputType::NO_INPUT) {

    if ($reqjson['mediaType'] === 'voip') {
        $promptsDefNps['base64EncodedG711ulawWithWavHeader'] = $reqjson['base64wavFile'];
        $promtsNps['prompts'] = array($promptsDefNps);
        $botExchangeResponse['nextPromptSequence'] = $promtsNps;
    } else {
        $promptsDefNps['transcript'] = 'text sent, ' . $reqjson['userInput'] . ", " . $reqjson['userInputType'];
        $promtsNps['prompts'] = array($promptsDefNps);
        $botExchangeResponse['nextPromptSequence'] = $promtsNps;
    }

    $botExchangeResponse['intentInfo'] = setIntent("WELCOME", 1.0);
} else if ($reqjson['userInputType'] === userInputType::TEXT) {

    if ($reqjson['userInput'] === 'error') {
        $botExchangeResponse['branchName'] = branchName::Error;

        $errorDetails['systemErrorMessage'] = 'error found';
        $botExchangeResponse['errorDetails'] = $errorDetails;
    } else {
        $promptsDefNps['transcript'] = $reqjson['userInput'];
        $promtsNps['prompts'] = array($promptsDefNps);
        $botExchangeResponse['nextPromptSequence'] = $promtsNps;
    }

    $botExchangeResponse['intentInfo'] = setIntent("WELCOME", 1.0);
} else if ($reqjson['userInputType'] === userInputType::BASE64_ENCODED_G711_ULAW_WAV_FILE) {

    if ($reqjson['base64wavFile'] !== '') {
        $promptsDefNps['base64EncodedG711ulawWithWavHeader'] = $reqjson['base64wavFile'];
        $promtsNps['prompts'] = array($promptsDefNps);
        $botExchangeResponse['nextPromptSequence'] = $promtsNps;
    } else {
        $promtsNps['prompts'] = array($promptsDefNps);
        $botExchangeResponse['nextPromptSequence'] = $promtsNps;
    }

    $botExchangeResponse['intentInfo'] = setIntent("WELCOME", 1.0);
} else if ($reqjson['userInputType'] === userInputType::AUTOMATED_TEXT) {

    $promptsDefNps['transcript'] = $reqjson['userInput'];
    $promtsNps['prompts'] = array($promptsDefNps);
    $botExchangeResponse['nextPromptSequence'] = $promtsNps;

    $botExchangeResponse['intentInfo'] = setIntent("WELCOME", 1.0);
} else if ($reqjson['userInputType'] === userInputType::DTMF_AS_TEXT) {

    $promptsDefNps['transcript'] = $reqjson['userInput'];
    $promtsNps['prompts'] = array($promptsDefNps);
    $botExchangeResponse['nextPromptSequence'] = $promtsNps;

    $botExchangeResponse['intentInfo'] = setIntent("WELCOME", 1.0);
}

echo json_encode($botExchangeResponse);


?>