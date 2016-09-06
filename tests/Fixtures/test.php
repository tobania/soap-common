<?php
return [
    'test' => [
        'testSOAP' => [
            'operations' => [
                'getSimple' => [
                    'action' => 'http://www.example.org/test/getSimple',
                    'style' => 'rpc',
                    'name' => 'getSimple',
                    'method' => 'getSimple',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\GetSimpleInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\GetSimpleInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\GetSimpleOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\GetSimpleOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'getMultiParam' => [
                    'action' => 'http://www.example.org/test/getMultiParam',
                    'style' => 'rpc',
                    'name' => 'getMultiParam',
                    'method' => 'getMultiParam',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\GetMultiParamInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\GetMultiParamInput',
                        'parts' => [
                            0 => 'parameters',
                            1 => 'other-param',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\GetMultiParamOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\GetMultiParamOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'getReturnMultiParam' => [
                    'action' => 'http://www.example.org/test/getReturnMultiParam',
                    'style' => 'rpc',
                    'name' => 'getReturnMultiParam',
                    'method' => 'getReturnMultiParam',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\GetReturnMultiParamInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\GetReturnMultiParamInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\GetReturnMultiParamOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\GetReturnMultiParamOutput',
                        'parts' => [
                            0 => 'parameters',
                            1 => 'other-param',
                        ],
                    ],
                    'fault' => [],
                ],
                'requestHeader' => [
                    'action' => 'http://www.example.org/test/requestHeader',
                    'style' => 'rpc',
                    'name' => 'requestHeader',
                    'method' => 'requestHeader',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\RequestHeaderInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\RequestHeaderInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\RequestHeaderOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\RequestHeaderOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'requestHeaders' => [
                    'action' => 'http://www.example.org/test/requestHeaders',
                    'style' => 'rpc',
                    'name' => 'requestHeaders',
                    'method' => 'requestHeaders',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\RequestHeadersInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\RequestHeadersInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\RequestHeadersOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\RequestHeadersOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'responseHader' => [
                    'action' => 'http://www.example.org/test/responseHader',
                    'style' => 'rpc',
                    'name' => 'responseHader',
                    'method' => 'responseHader',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\ResponseHaderInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\ResponseHaderInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\ResponseHaderOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\ResponseHaderOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'responseFault' => [
                    'action' => 'http://www.example.org/test/responseFault',
                    'style' => 'rpc',
                    'name' => 'responseFault',
                    'method' => 'responseFault',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\ResponseFaultInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\ResponseFaultInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\ResponseFaultOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\ResponseFaultOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'responseFaults' => [
                    'action' => 'http://www.example.org/test/responseFaults',
                    'style' => 'rpc',
                    'name' => 'responseFaults',
                    'method' => 'responseFaults',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\ResponseFaultsInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\ResponseFaultsInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\ResponseFaultsOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\ResponseFaultsOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'noInput' => [
                    'action' => 'http://www.example.org/test/noInput',
                    'style' => 'rpc',
                    'name' => 'noInput',
                    'method' => 'noInput',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\NoInputInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\NoInputInput',
                        'parts' => [],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\NoInputOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\NoInputOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
                'noOutput' => [
                    'action' => 'http://www.example.org/test/noOutput',
                    'style' => 'rpc',
                    'name' => 'noOutput',
                    'method' => 'noOutput',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\NoOutputInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\NoOutputInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\NoOutputOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\NoOutputOutput',
                        'parts' => [],
                    ],
                    'fault' => [],
                ],
                'noBoth' => [
                    'action' => 'http://www.example.org/test/noBoth',
                    'style' => 'rpc',
                    'name' => 'noBoth',
                    'method' => 'noBoth',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\NoBothInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\NoBothInput',
                        'parts' => [],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\NoBothOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\NoBothOutput',
                        'parts' => [],
                    ],
                    'fault' => [],
                ],
            ],
            'endpoint' => 'http://www.example.org/',
        ],
    ],
    'alternativeTest' => [
        'aPort' => [
            'operations' => [],
            'endpoint' => 'http://www.example.org/',
        ],
        'otherPort' => [
            'operations' => [
                'doSomething' => [
                    'action' => 'http://www.example.org/test/doSomething',
                    'style' => 'rpc',
                    'name' => 'doSomething',
                    'method' => 'doSomething',
                    'input' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\DoSomethingInput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\DoSomethingInput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'output' => [
                        'message_fqcn' => '\\SoapEnvelope\\Messages\\DoSomethingOutput',
                        'part_fqcn' => '\\SoapEnvelope\\Parts\\DoSomethingOutput',
                        'parts' => [
                            0 => 'parameters',
                        ],
                    ],
                    'fault' => [],
                ],
            ],
            'endpoint' => 'http://www.example.org/',
        ],
        'http' => [
            'operations' => [],
            'endpoint' => NULL,
        ],
    ],
];
