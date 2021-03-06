<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9ca02ded2ffb55c884817c3c88c86343
{
    public static $files = array (
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '0d59ee240a4cd96ddbb4ff164fccea4d' => __DIR__ . '/..' . '/symfony/polyfill-php73/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Php73\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Contracts\\Service\\' => 26,
            'Symfony\\Contracts\\HttpClient\\' => 29,
            'Symfony\\Component\\HttpClient\\' => 29,
            'Symfony\\Component\\DomCrawler\\' => 29,
            'Symfony\\Component\\BrowserKit\\' => 29,
            'Svg\\' => 4,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Container\\' => 14,
            'Picqer\\Barcode\\' => 15,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'L' => 
        array (
            'Linfo\\' => 6,
        ),
        'F' => 
        array (
            'FontLib\\' => 8,
            'Firebase\\JWT\\' => 13,
        ),
        'D' => 
        array (
            'Dompdf\\' => 7,
        ),
        'A' => 
        array (
            'Adianti\\Plugins\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Php73\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php73',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Contracts\\Service\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/service-contracts',
        ),
        'Symfony\\Contracts\\HttpClient\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/http-client-contracts',
        ),
        'Symfony\\Component\\HttpClient\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/http-client',
        ),
        'Symfony\\Component\\DomCrawler\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/dom-crawler',
        ),
        'Symfony\\Component\\BrowserKit\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/browser-kit',
        ),
        'Svg\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-svg-lib/src/Svg',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Picqer\\Barcode\\' => 
        array (
            0 => __DIR__ . '/..' . '/picqer/php-barcode-generator/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Linfo\\' => 
        array (
            0 => __DIR__ . '/..' . '/linfo/linfo/src/Linfo',
        ),
        'FontLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-font-lib/src/FontLib',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Dompdf\\' => 
        array (
            0 => __DIR__ . '/..' . '/dompdf/dompdf/src',
        ),
        'Adianti\\Plugins\\' => 
        array (
            0 => __DIR__ . '/..' . '/adianti/plugins/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'TPDFDesigner' => 
            array (
                0 => __DIR__ . '/..' . '/adianti/pdfdesigner',
            ),
        ),
        'S' => 
        array (
            'Spreadsheet' => 
            array (
                0 => __DIR__ . '/..' . '/pablodalloglio/spreadsheet_excel_writer',
            ),
            'Sabberworm\\CSS' => 
            array (
                0 => __DIR__ . '/..' . '/sabberworm/php-css-parser/lib',
            ),
        ),
        'P' => 
        array (
            'PHPRtfLite' => 
            array (
                0 => __DIR__ . '/..' . '/phprtflite/phprtflite/lib',
            ),
        ),
        'O' => 
        array (
            'OLE' => 
            array (
                0 => __DIR__ . '/..' . '/pablodalloglio/ole',
            ),
        ),
        'F' => 
        array (
            'FPDF' => 
            array (
                0 => __DIR__ . '/..' . '/pablodalloglio/fpdf',
            ),
        ),
        'B' => 
        array (
            'BaconQrCode' => 
            array (
                0 => __DIR__ . '/..' . '/bacon/bacon-qr-code/src',
            ),
        ),
        'A' => 
        array (
            'AdiantiPDFDesigner' => 
            array (
                0 => __DIR__ . '/..' . '/adianti/pdfdesigner',
            ),
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Dompdf\\Cpdf' => __DIR__ . '/..' . '/dompdf/dompdf/lib/Cpdf.php',
        'HTML5_Data' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Data.php',
        'HTML5_InputStream' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/InputStream.php',
        'HTML5_Parser' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Parser.php',
        'HTML5_Tokenizer' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Tokenizer.php',
        'HTML5_TreeBuilder' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/TreeBuilder.php',
        'JsonException' => __DIR__ . '/..' . '/symfony/polyfill-php73/Resources/stubs/JsonException.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
        'pQuery' => __DIR__ . '/..' . '/tburry/pquery/pQuery.php',
        'pQuery\\AspEmbeddedNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\CSSQueryTokenizer' => __DIR__ . '/..' . '/tburry/pquery/gan_selector_html.php',
        'pQuery\\CdataNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\CommentNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\ConditionalTagNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\DoctypeNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\DomNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\EmbeddedNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\Html5Parser' => __DIR__ . '/..' . '/tburry/pquery/gan_parser_html.php',
        'pQuery\\HtmlFormatter' => __DIR__ . '/..' . '/tburry/pquery/gan_formatter.php',
        'pQuery\\HtmlParser' => __DIR__ . '/..' . '/tburry/pquery/gan_parser_html.php',
        'pQuery\\HtmlParserBase' => __DIR__ . '/..' . '/tburry/pquery/gan_parser_html.php',
        'pQuery\\HtmlSelector' => __DIR__ . '/..' . '/tburry/pquery/gan_selector_html.php',
        'pQuery\\IQuery' => __DIR__ . '/..' . '/tburry/pquery/IQuery.php',
        'pQuery\\TextNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
        'pQuery\\TokenizerBase' => __DIR__ . '/..' . '/tburry/pquery/gan_tokenizer.php',
        'pQuery\\XML2ArrayParser' => __DIR__ . '/..' . '/tburry/pquery/gan_xml2array.php',
        'pQuery\\XmlNode' => __DIR__ . '/..' . '/tburry/pquery/gan_node_html.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9ca02ded2ffb55c884817c3c88c86343::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9ca02ded2ffb55c884817c3c88c86343::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit9ca02ded2ffb55c884817c3c88c86343::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit9ca02ded2ffb55c884817c3c88c86343::$classMap;

        }, null, ClassLoader::class);
    }
}
