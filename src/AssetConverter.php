<?php

namespace fafcms\helpers;

use Yii;
use yii\base\Exception;
use yii\web\AssetConverterInterface;

class AssetConverter extends \yii\web\AssetConverter implements AssetConverterInterface
{
    /**
     * {@inheritdoc}
     */
    protected function runCommand($command, $basePath, $asset, $result)
    {
        $command = Yii::getAlias($command);
        $targetPath = $basePath.'/'.substr($asset, 0, (strlen($asset) - strlen(strrchr($asset, '/'))));

        $command = strtr($command, [
            '{from}' => escapeshellarg("$basePath/$asset"),
            '{targetpath}' => $targetPath,
            '{to}' => escapeshellarg("$basePath/$result"),
        ]);

        $descriptor = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];
        $proc = proc_open($command, $descriptor, $pipes, $basePath);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $status = proc_close($proc);

        if ($status === 0) {
            Yii::debug("Converted $asset into $result:\nSTDOUT:\n$stdout\nSTDERR:\n$stderr", __METHOD__);
        } elseif (YII_DEBUG) {
            throw new Exception("AssetConverter command '$command' failed with exit code $status:\nSTDOUT:\n$stdout\nSTDERR:\n$stderr");
        } else {
            Yii::error("AssetConverter command '$command' failed with exit code $status:\nSTDOUT:\n$stdout\nSTDERR:\n$stderr", __METHOD__);
        }

        return $status === 0;
    }
}
