<?php

declare(strict_types=1);

include('../vendor/autoload.php');

use Faf\TemplateEngine\Parser;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;
use Yiisoft\Cache\Cache;
use Yiisoft\Cache\Apcu\ApcuCache;

$logFile = __DIR__ . DIRECTORY_SEPARATOR . 'parser.log';
@unlink($logFile);

$logger = new Logger([
  new FileTarget($logFile)
]);

$cache = new Cache(new ApcuCache());

$fafte = new Parser([
    'logger' => $logger,
    'cache' => $cache,
    'mode' => Parser::MODE_DEV,
    'language' => 'de_DE'
]);

$data = require('sample-data.php');
$data['parser'] = $fafte;

$fafte->setData($data);

$demoDir = 'demos';
$demos = scandir($demoDir);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Finally a fast - Template Engine - PHP</title>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.7/dist/semantic.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.7/dist/semantic.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/ace-builds@1.4.12/src-min/ace.js" integrity="sha256-Q9hnBpgBFstzZOr+OKFOWZWfcF5nFXO8Qz48Nmndo6U=" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                $('.ui.sidebar')
                    .sidebar({
                        context: $('.main-content')
                    })
                    .sidebar('setting', 'dimPage', false)
                    .sidebar('setting', 'closable', false)
                    .sidebar('attach events', '.toc.item')

                $('.editor').each(function () {
                    var editor = ace.edit(this)
                    editor.setTheme('ace/theme/nord_dark')
                    editor.session.setMode('ace/mode/php')

                    if ($(this).hasClass('readonly')) {
                        editor.setReadOnly(true)
                    } else {
                        var textarea = $('textarea[name="code"]')
                        textarea.val(editor.getSession().getValue())

                        editor.getSession().on('change', function () {
                            textarea.val(editor.getSession().getValue())
                        })
                    }

                    editor.setShowPrintMargin(false)
                    editor.setOption('minLines', 5)
                    editor.setOption('maxLines', Number.MAX_VALUE)
                    editor.setOption('tabSize', 4)
                    editor.setOption('useSoftTabs', true)
                    editor.setOption('showInvisibles', true)
                    editor.session.setUseWrapMode(true)
                })

                var frame = $('#frame-content').get(0)

                if (frame.contentDocument) {
                    frame.document = frame.contentDocument;
                }

                if (frame.document !== null) {
                    frame.document.open();
                    frame.document.writeln('<style>*{font: 12px/normal \'Monaco\', \'Menlo\', \'Ubuntu Mono\', \'Consolas\', \'source-code-pro\', monospace;color:#ffffff;</style>' + $('#raw-content').text());
                    frame.document.close();
                }
            })
        </script>
        <style>
            body {
                padding-top: 40px;
            }

            .main-content .container {
                margin: 3em !important;
                width: calc(100% - 260px - 6em) !important;
            }

            .main-content {
                background-color: #3e4658 !important;
                height: auto;
                min-height: 100%;
            }

            .ui.inverted.menu.sidebar, .ui.inverted.segment, .ui.primary.inverted.segment {
                background-color: #2e3440;
            }

            .ui.inverted.menu.fixed {
                background-color: #151a22;
            }

            #frame-content {
                background-color: #2e3440;
                border: 1px solid #151a22;
                width: 100%;
                height: 25vh;
            }

            .full.height {
                display: -webkit-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
                -webkit-flex-direction: row;
                -ms-flex-direction: row;
                flex-direction: row;
            }

            .editor, .ui.inverted.segment, .ui.primary.inverted.segment {
                border: 1px solid #151a22;
                border-radius: 0;
            }
        </style>
    </head>
    <body>
        <div class="ui fixed inverted menu">
            <a class="toc item">
                <i class="sidebar icon"></i>
            </a>
            <span class="header item">
                Finally a fast - Template Engine - PHP
            </span>
        </div>
        <div class="ui pushable main-content">
            <div class="ui visible inverted left vertical sidebar menu">
                <a class="item" href=".">Sandbox</a>
                <?php
                foreach ($demos as $demo) {
                    if ($demo === '.' || $demo === '..') {
                        continue;
                    }

                    $demoContent = file_get_contents($demoDir . DIRECTORY_SEPARATOR . $demo);

                    echo '<a class="item" href="?demo=' . urlencode($demo) . '">' . ucwords(str_replace('-', ' ', str_replace('.html', '', $demo))) . '</a>';
                }
                ?>
            </div>
            <div class="pusher">
                <div class="full height">
                    <div class="ui container fluid">
                        <?php
                        if (($_GET['demo'] ?? null) !== null) {
                            $demo = $_GET['demo'] ?? null;

                            $code = file_get_contents($demoDir . DIRECTORY_SEPARATOR . $demo);
                            $title = ucwords(str_replace('-', ' ', str_replace('.html', '', $demo)));
                        } else {
                            $code = $_POST['code'] ?? '';
                            $title = 'Sandbox';
                        }

                        $paredContent = $fafte->parse($code);
                        $logger->flush(true);
                        ?>
                        <form action="." method="post">
                            <h1 class="ui dividing inverted header"><?= $title ?></h1>
                            <h2 class="ui inverted header">Code</h2>
                            <pre class="editor"><?= htmlentities($code) ?></pre>
                            <textarea name="code" style="display: none;"></textarea>
                            <button type="submit" class="ui right labeled icon button inverted">
                                <i class="right arrow icon"></i>
                                Run
                            </button>
                            <h2 class="ui inverted header">Raw result</h2>
                            <pre class="editor readonly"><?= htmlentities($paredContent) ?></pre>
                            <h2 class="ui inverted header">Result</h2>
                            <textarea id="raw-content" style="display: none"><?= $paredContent ?></textarea>
                            <iframe id="frame-content" src="#"></iframe>
                            <h2 class="ui inverted header">Log</h2>
                            <pre class="editor readonly"><?= file_get_contents($logFile) ?></pre>
                            <h2 class="ui inverted header">Executed code</h2>
                            <?php
                                $code = var_export($code, true);
                                $data = var_export(require('sample-data.php'), true);

                                $executedCode = <<<PHP
                                <?php

                                use Faf\TemplateEngine\Parser;
                                use Yiisoft\Log\Logger;
                                use Yiisoft\Log\Target\File\FileTarget;
                                use Yiisoft\Cache\Cache;
                                use Yiisoft\Cache\Apcu\ApcuCache;

                                \$logger = new Logger([
                                    new FileTarget('$logFile')
                                ]);

                                \$cache = new Cache(new ApcuCache());

                                \$fafte = new Parser([
                                    'logger'   => \$logger,                      // any PSR-16 cache @see https://www.php-fig.org/psr/psr-16/
                                    'cache'    => \$cache,                       // any PSR-3 logger @see https://www.php-fig.org/psr/psr-3/
                                    'mode'     => Parser::MODE_DEV,         // you should remove this line to use production mode
                                    'language' => 'de_DE',                       // any ICU locale @see https://icu4c-demos.unicode.org/icu-bin/locexp
                                    //'data'     => ['string' => 'Test string']  // you can pass any data to the parser
                                ]);

                                // you can also pass any option with the corresponding setter function
                                \$fafte->setData($data);

                                echo \$fafte->parse($code);
                                PHP;

                                echo '<pre class="editor readonly">' . htmlentities($executedCode) . '</pre>';
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
