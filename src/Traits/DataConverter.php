<?php

namespace Awesome\Connector\Traits;

use GuzzleHttp\Psr7\MultipartStream;

trait DataConverter
{
    protected function toJson(array $data): string
    {
        return json_encode($data);
    }

    protected function toMultipartStream(array $data): MultipartStream
    {
        return new MultipartStream($this->transformToMultipartData($data));
    }

    protected function transformToMultipartData(array $data, string $contentName = ''): array
    {
        $res = [];

        foreach ($data as $k => $v) {
            $name = empty($contentName) ? "{$k}" : "{$contentName}{$k}";

            if (is_array($v)) {
                $res = array_merge($res, $this->transformToMultipartData($v, $name));
                continue;
            }

            if ($v instanceof \SplFileInfo) {
                $res[] = [
                    'name' => $name,
                    'filename' => $v->getFilename(),
                    'contents' => file_get_contents($v->getRealPath()),
                    'headers' => [
                        'content-type' => 'application/octet-stream'
                    ]
                ];
                continue;
            }

            $res[] = [
                'name' => $name,
                'contents' => $v
            ];
        }

        return $res;
    }
}
