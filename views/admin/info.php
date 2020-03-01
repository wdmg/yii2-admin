<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/admin', 'System information');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="admin-info">
    <?= DetailView::widget([
        'model' => $data,
        'attributes' => [
            'hosr' => [
                'label' => Yii::t('app/modules/admin', "IP, host"),
                'value' => function ($data) {
                    return $data['server'] . " (". $data['ip'] .":". $data['port'] .")" . ((isset($data['host'])) ? ", " . $data['host'] : "");
                }
            ],
            'server' => [
                'label' => Yii::t('app/modules/admin', "Protocol, server"),
                'value' => function ($data) {
                    return "HTTP " . $data['protocol'] . ((isset($data['engine'])) ? ", " . $data['engine'] : "");
                }
            ],
            'charset' => [
                'label' => Yii::t('app/modules/admin', "Charset and language"),
                'value' => function ($data) {
                    return $data['charset'] . ", " . $data['language'];
                }
            ],

            'memory_limit' => [
                'label' => Yii::t('app/modules/admin', "Memory limit"),
                'value' => function ($data) {
                    return $data['memory_limit'];
                }
            ],
            'upload_max_filesize' => [
                'label' => Yii::t('app/modules/admin', "Upload max filesize"),
                'value' => function ($data) {
                    return $data['upload_max_filesize'];
                }
            ],
            'post_max_size' => [
                'label' => Yii::t('app/modules/admin', "Post max size"),
                'value' => function ($data) {
                    return $data['post_max_size'];
                }
            ],

            'max_input_time' => [
                'label' => Yii::t('app/modules/admin', "Max input time"),
                'value' => function ($data) {
                    return $data['max_input_time'] . " sec.";
                }
            ],
            'max_execution_time' => [
                'label' => Yii::t('app/modules/admin', "Max execution time"),
                'value' => function ($data) {
                    return $data['max_execution_time'] . " sec.";
                }
            ],

            'client' => [
                'label' => Yii::t('app/modules/admin', "Client"),
                'value' => function ($data) {
                    return $data['client'];
                }
            ],

            'phpVersion' => [
                'label' => Yii::t('app/modules/admin', "PHP version"),
                'value' => function ($data) {
                    return $data['phpVersion'];
                }
            ],
            'dbVersion' => [
                'label' => Yii::t('app/modules/admin', "DB version"),
                'value' => function ($data) {
                    return $data['dbVersion']['type'] . ", ". $data['dbVersion']['version'];
                }
            ],
            'yiiVersion' => [
                'label' => Yii::t('app/modules/admin', "Yii-framework version"),
                'value' => function ($data) {
                    return $data['yiiVersion'];
                }
            ],
            'version' => [
                'label' => Yii::t('app/modules/admin', "Butterfly.CMS version"),
                'value' => function ($data) {
                    return $data['application']['version'];
                }
            ],
            'datetime' => [
                'label' => Yii::t('app/modules/admin', "Server time"),
                'value' => function ($data) {
                    return $data['datetime']['datetime'] . ((isset($data['datetime']['timezone'])) ? " (". $data['datetime']['timezone'] . ")" : "");
                }
            ],
            'uptime' => [
                'label' => Yii::t('app/modules/admin', "Server runs"),
                'value' => function ($data) {
                    return Yii::t(
                        'app/modules/admin',
                        '{days, plural, =0{} one {# day, } few {# days, } many {# days, } other {# days, }}{hours, plural, =0{} one {# hour, } few {# hours, } many {# hours, } other {# hours, }}{minutes, plural, =0{} one {# minute, } few {# minutes, } many {# minutes, } other {# minutes, }}{seconds, plural, =0{} one {# second} few {# seconds} many {# seconds} other {# seconds}}',
                        [
                            'days' => (isset($data['uptime']['days'])) ? $data['uptime']['days'] : 0,
                            'hours' => (isset($data['uptime']['hours'])) ? $data['uptime']['hours'] : 0,
                            'minutes' => (isset($data['uptime']['minutes'])) ? $data['uptime']['minutes'] : 0,
                            'seconds' => (isset($data['uptime']['seconds'])) ? $data['uptime']['seconds'] : 0
                        ]
                    );
                }
            ]
        ],
    ]);
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">
                <a data-toggle="collapse" href="#phpExtensions">
                    <?= Yii::t('app/modules/admin', "PHP extensions") ?>
                </a>
            </h5>
        </div>
        <div id="phpExtensions" class="panel-collapse collapse">
            <div class="panel-body">
                <?= DetailView::widget([
                    'model' => $data,
                    'attributes' => [
                        'openssl' => [
                            'label' => Yii::t('app/modules/admin', "OpenSSL"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['openssl']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'curl' => [
                            'label' => Yii::t('app/modules/admin', "cURL"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['curl']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'imap' => [
                            'label' => Yii::t('app/modules/admin', "IMAP"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['imap']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'simplexml' => [
                            'label' => Yii::t('app/modules/admin', "SimpleXML"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['simplexml']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'ftp' => [
                            'label' => Yii::t('app/modules/admin', "FTP"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['ftp']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'ssh2' => [
                            'label' => Yii::t('app/modules/admin', "SSH2"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['ssh2']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'exif' => [
                            'label' => Yii::t('app/modules/admin', "EXIF"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['exif']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'soap' => [
                            'label' => Yii::t('app/modules/admin', "SOAP"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['soap']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'sockets' => [
                            'label' => Yii::t('app/modules/admin', "Sockets"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['sockets']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'uploadprogress' => [
                            'label' => Yii::t('app/modules/admin', "UploadProgress"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['uploadprogress']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'oauth' => [
                            'label' => Yii::t('app/modules/admin', "oAuth"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['oauth']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'gmp' => [
                            'label' => Yii::t('app/modules/admin', "GMP"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['gmp']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'zip' => [
                            'label' => Yii::t('app/modules/admin', "ZIP"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['zip']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'zlib' => [
                            'label' => Yii::t('app/modules/admin', "Zlib"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['zlib']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'pdflib' => [
                            'label' => Yii::t('app/modules/admin', "PDFLib"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['pdflib']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'xdebug' => [
                            'label' => Yii::t('app/modules/admin', "xDebug"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['xdebug']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'apc' => [
                            'label' => Yii::t('app/modules/admin', "APC"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['apc']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'apcu' => [
                            'label' => Yii::t('app/modules/admin', "APC User Cache"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['apcu']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'memcache' => [
                            'label' => Yii::t('app/modules/admin', "Memcache"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['memcache']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'memcached' => [
                            'label' => Yii::t('app/modules/admin', "Memcached"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['memcached']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'opcache' => [
                            'label' => Yii::t('app/modules/admin', "OPcache"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['opcache']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'iconv' => [
                            'label' => Yii::t('app/modules/admin', "iConv"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['iconv']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'intl' => [
                            'label' => Yii::t('app/modules/admin', "INTL"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['intl']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'imagick' => [
                            'label' => Yii::t('app/modules/admin', "Imagick"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['imagick']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'gd' => [
                            'label' => Yii::t('app/modules/admin', "GD"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['gd']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],
                        'smtp' => [
                            'label' => Yii::t('app/modules/admin', "SMTP"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['smtp']) ? '<span class="fa fa-check text-success"></span>' : '<span class="fa fa-remove text-danger"></span>';
                            }
                        ],

                        'expose_php' => [
                            'label' => Yii::t('app/modules/admin', "Expose php"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['expose_php']) ? '<span class="fa fa-check text-danger"></span>' : '<span class="fa fa-remove text-success"></span>';
                            }
                        ],
                        'allow_url_include' => [
                            'label' => Yii::t('app/modules/admin', "Allow url include"),
                            'format' => 'raw',
                            'value' => function ($data) {
                                return ($data['php']['allow_url_include']) ? '<span class="fa fa-check text-danger"></span>' : '<span class="fa fa-remove text-success"></span>';
                            }
                        ],

                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
