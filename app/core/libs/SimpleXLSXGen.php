<?php

/** @noinspection UnknownInspectionInspection */
/* PHP5.6 */
/** @noinspection PowerOperatorCanBeUsedInspection */
/** @noinspection HttpUrlsUsage */
/* PHP7 */
/** @noinspection NullCoalescingOperatorCanBeUsedInspection */
/** @noinspection PhpIssetCanBeReplacedWithCoalesceInspection */
/* PHP8 */
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpMissingClassConstantTypeInspection */
/** @noinspection PhpMissingFieldTypeInspection */
/** @noinspection PhpMissingParamTypeInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection PhpStrFunctionsInspection */
/** @noinspection AccessModifierPresentedInspection */

namespace Shuchkin;
/**
 * Class SimpleXLSXGen
 * Export data to MS Excel. PHP XLSX generator
 * Author: sergey.shuchkin@gmail.com
 */
class SimpleXLSXGen
{
    public $curSheet;
    protected $defaultFont;
    protected $defaultFontSize;
    protected $rtl;
    protected $sheets;
    protected $template;
    protected $NF; // numFmts
    protected $NF_KEYS;
    protected $XF; // cellXfs
    protected $XF_KEYS;
    protected $BR_STYLE;
    protected $SI; // shared strings
    protected $SI_KEYS;
//    protected $rIndex;

    protected $title;
    protected $subject;
    protected $author;
    protected $company;
    protected $manager;
    protected $description;
    protected $application;
    protected $keywords;
    protected $category;
    protected $language;
    protected $lastModifiedBy;
    const N_NORMAL = 0; // General
    const N_INT = 1; // 0
    const N_DEC = 2; // 0.00
    const N_PERCENT_INT = 9; // 0%
    const N_PRECENT_DEC = 10; // 0.00%
    const N_DATE = 14; // mm-dd-yy
    const N_TIME = 20; // h:mm
    const N_RUB = 164;
    const N_DOLLAR = 165;
    const N_EURO = 166;
    const N_DATETIME = 22; // m/d/yy h:mm
    const N_CUSTOM = 197;
    const F_NORMAL = 0;
    const F_HYPERLINK = 1;
    const F_BOLD = 2;
    const F_ITALIC = 4;
    const F_UNDERLINE = 8;
    const F_STRIKE = 16;
    const F_COLOR = 32;
    const FL_NONE = 0; // none
    const FL_SOLID = 1; // solid
    const FL_MEDIUM_GRAY = 2; // mediumGray
    const FL_DARK_GRAY = 4; // darkGray
    const FL_LIGHT_GRAY = 8; // lightGray
    const FL_GRAY_125 = 16; // gray125
    const FL_COLOR = 32;
    const A_DEFAULT = 0;
    const A_LEFT = 1;
    const A_RIGHT = 2;
    const A_CENTER = 4;
    const A_TOP = 8;
    const A_MIDDLE = 16;
    const A_BOTTOM = 32;
    const A_WRAPTEXT = 64;
    const A_ROTATION_90 = 128;
    const B_NONE = 0;
    const B_THIN = 1;
    const B_MEDIUM = 2;
    //const
    const B_DASHED = 3;
    const B_DOTTED = 4;
    const B_THICK = 5;
    const B_DOUBLE = 6;
    const B_HAIR = 7;
    const B_MEDIUM_DASHED = 8;
    const B_DASH_DOT = 9;
    const B_MEDIUM_DASH_DOT = 10;
    const B_DASH_DOT_DOT = 11;
    const B_MEDIUM_DASH_DOT_DOT = 12;
    const B_SLANT_DASH_DOT = 13;

    public function __construct()
    {
        $this->subject = '';
        $this->title = '';
        $this->author = '';
        $this->company = '';
        $this->manager = '';
        $this->description = '';
        $this->keywords = '';
        $this->category = '';
        $this->language = 'en-US';
        $this->lastModifiedBy = '';
        $this->application = __CLASS__;

        $this->curSheet = -1;
        $this->defaultFont = 'Calibri';
        $this->defaultFontSize = 10;
        $this->rtl = false;
        $this->sheets = [];
        $this->SI = [];        // sharedStrings index
        $this->SI_KEYS = []; //  & keys

        // https://c-rex.net/projects/samples/ooxml/e1/Part4/OOXML_P4_DOCX_numFmts_topic_ID0E6KK6.html
        $this->NF = [
            self::N_RUB => '#,##0.00\ "₽"',
            self::N_DOLLAR => '[$$-1]#,##0.00',
            self::N_EURO => '#,##0.00\ [$€-1]'
        ];
        $this->NF_KEYS = array_flip($this->NF);

        $this->BR_STYLE = [
            self::B_NONE => 'none',
            self::B_THIN => 'thin',
            self::B_MEDIUM => 'medium',
            self::B_DASHED => 'dashed',
            self::B_DOTTED => 'dotted',
            self::B_THICK => 'thick',
            self::B_DOUBLE => 'double',
            self::B_HAIR => 'hair',
            self::B_MEDIUM_DASHED => 'mediumDashed',
            self::B_DASH_DOT => 'dashDot',
            self::B_MEDIUM_DASH_DOT => 'mediumDashDot',
            self::B_DASH_DOT_DOT => 'dashDotDot',
            self::B_MEDIUM_DASH_DOT_DOT => 'mediumDashDotDot',
            self::B_SLANT_DASH_DOT => 'slantDashDot'
        ];

        $this->XF = [  // styles 0 - num fmt, 1 - align, 2 - font, 3 - fill, 4 - font color, 5 - bgcolor, 6 - border, 7 - font size
            [self::N_NORMAL, self::A_DEFAULT, self::F_NORMAL, self::FL_NONE, 0, 0, '', 0],
            [self::N_NORMAL, self::A_DEFAULT, self::F_NORMAL, self::FL_GRAY_125, 0, 0, '', 0], // hack
        ];
        $this->XF_KEYS[implode('-', $this->XF[0])] = 0; // & keys
        $this->XF_KEYS[implode('-', $this->XF[1])] = 1;
        $this->template = [
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'."\r\n"
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'."\r\n"
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'."\r\n"
                .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'."\r\n"
                .'</Relationships>',
            'docProps/app.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">'."\r\n"
                .'<TotalTime>0</TotalTime>'."\r\n"
                .'<Application>{APP}</Application>'."\r\n"
                .'<Company>{COMPANY}</Company>'."\r\n"
                .'<Manager>{MANAGER}</Manager>'."\r\n"
                .'</Properties>',
            'docProps/core.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'."\r\n"
                .'<dcterms:created xsi:type="dcterms:W3CDTF">{DATE}</dcterms:created>'."\r\n"
                .'<dc:title>{TITLE}</dc:title>'."\r\n"
                .'<dc:subject>{SUBJECT}</dc:subject>'."\r\n"
                .'<dc:creator>{AUTHOR}</dc:creator>'."\r\n"
                .'<cp:lastModifiedBy>{LAST_MODIFY_BY}</cp:lastModifiedBy>'."\r\n"
                .'<cp:keywords>{KEYWORD}</cp:keywords>'."\r\n"
                .'<dc:description>{DESCRIPTION}</dc:description>'."\r\n"
                .'<cp:category>{CATEGORY}</cp:category>'."\r\n"
                .'<dc:language>{LANGUAGE}</dc:language>'."\r\n"
                .'<dcterms:modified xsi:type="dcterms:W3CDTF">{DATE}</dcterms:modified>'."\r\n"
                .'<cp:revision>1</cp:revision>'."\r\n"
                .'</cp:coreProperties>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                ."\r\n{RELS}\r\n</Relationships>",
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                . '<worksheet' . "\r\n"
                . ' xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"' . "\r\n"
                . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'."\r\n"
                . ' xmlns:mx="http://schemas.microsoft.com/office/mac/excel/2008/main"' . "\r\n"
                . ' xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006"' . "\r\n"
                . ' xmlns:mv="urn:schemas-microsoft-com:mac:vml"' . "\r\n"
                . ' xmlns:x14="http://schemas.microsoft.com/office/spreadsheetml/2009/9/main"' . "\r\n"
                . ' xmlns:x15="http://schemas.microsoft.com/office/spreadsheetml/2010/11/main"' . "\r\n"
                . ' xmlns:x14ac="http://schemas.microsoft.com/office/spreadsheetml/2009/9/ac"' . "\r\n"
                . ' xmlns:xm="http://schemas.microsoft.com/office/excel/2006/main">' . "\r\n"
                .'<dimension ref="{REF}"/>'."\r\n"
                ."{SHEETVIEWS}\r\n{COLS}\r\n{ROWS}\r\n{AUTOFILTER}{MERGECELLS}{HYPERLINKS}{VML}</worksheet>",
            'xl/worksheets/_rels/sheet1.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">{RELS}</Relationships>',
            'xl/sharedStrings.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="{CNT}" uniqueCount="{CNT}">{STRINGS}</sst>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                ."\r\n{NUMFMTS}\r\n{FONTS}\r\n{FILLS}\r\n{BORDERS}\r\n"
                .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" /></cellStyleXfs>'
                ."\r\n{XF}\r\n"
                .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'."\r\n"
                .'<fileVersion appName="{APP}"/><sheets>{SHEETS}</sheets></workbook>',
            'xl/comments1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                . '<comments' . "\r\n"
                . ' xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"' . "\r\n"
                . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\r\n"
                . '<authors>{AUTHORS}</authors>' . "\r\n"
                . '<commentList>{COMMENTS}</commentList>' . "\r\n"
                . '</comments>',
            'xl/drawings/vmlDrawing1.vml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\r\n"
                . '<xml xmlns:p="http://schemas.openxmlformats.org/presentationml/2006/main" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:oa="urn:schemas-microsoft-com:office:activation" xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\r\n"
                .'<o:shapelayout v:ext="edit"><o:idmap data="1" v:ext="edit"/></o:shapelayout>' . "\r\n"
                .'<v:shapetype id="_x0000_t202" coordsize="21600,21600" o:spt="202.0" path="m,l,21600r21600,l21600,xe"><v:stroke joinstyle="miter"/><v:path o:connecttype="rect" gradientshapeok="t"/></v:shapetype>{SHAPES}</xml>',
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n"
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'."\r\n"
                .'<Default Extension="vml" ContentType="application/vnd.openxmlformats-officedocument.vmlDrawing"/>' . "\r\n"
                .'<Default Extension="xml" ContentType="application/xml"/>' . "\r\n"
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' . "\r\n"
                .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'."\r\n"
                .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'."\r\n"
                .'<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'."\r\n"
                .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                ."\r\n{TYPES}</Types>",
        ];
    }
    public static function create($title = null)
    {
        $xlsx = new static();
        if ($title) {
            $xlsx->setTitle($title);
        }
        return $xlsx;
    }

    public static function fromArray(array $rows, $sheetName = null)
    {
        $xlsx = new static();
        $xlsx->addSheet($rows, $sheetName);
        if ($sheetName) {
            $xlsx->setTitle($sheetName);
        }
        return $xlsx;
    }

    public function addSheet(array $rows, $name = null)
    {
        $this->curSheet++;
        if ($name === null) { // autogenerated sheet names
            $name = ($this->title ? mb_substr($this->title, 0, 31) : 'Sheet') . ($this->curSheet + 1);
        } else {
            $name = mb_substr($name, 0, 31);
            $names = [];
            foreach ($this->sheets as $sh) {
                $names[mb_strtoupper($sh['name'])] = 1;
            }
            for ($i = 0; $i < 100; $i++) {
                $postfix = ' (' . $i . ')';
                $new_name = ($i === 0) ? $name : $name . $postfix;
                if (mb_strlen($new_name) > 31) {
                    $new_name = mb_substr($name, 0, 31 - mb_strlen($postfix)) . $postfix;
                }
                $NEW_NAME = mb_strtoupper($new_name);
                if (!isset($names[$NEW_NAME])) {
                    $name = $new_name;
                    break;
                }
            }
        }
        $this->sheets[$this->curSheet] = ['name' => $name, 'relidx' => 0, 'hyperlinks' => [], 'comments' => [], 'mergecells' => [], 'colwidth' => [], 'autofilter' => '', 'frozen' => ''];
        if (isset($rows[0]) && is_array($rows[0])) {
            $this->sheets[$this->curSheet]['rows'] = $rows;
        } else {
            $this->sheets[$this->curSheet]['rows'] = [];
        }
        return $this;
    }

    public function downloadAs($filename)
    {
        $fh = fopen('php://memory', 'wb');
        if (!$fh) {
            return false;
        }
        if (!$this->_write($fh)) {
            fclose($fh);
            return false;
        }
        $size = ftell($fh);
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . str_replace(["\0","\r","\n","\t",'"'], '', $filename) . '"');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));
        header('Content-Length: ' . $size);
        while (ob_get_level()) {
            ob_end_clean();
        }
        fseek($fh, 0);
        if (function_exists('fpassthru')) {
            fpassthru($fh);
        } else {
            echo stream_get_contents($fh);
        }
        fclose($fh);
        return true;
    }

    protected function _write($fh)
    {
        $dirSignatureE = "\x50\x4b\x05\x06"; // end of central dir signature
        $zipComments = 'Generated by ' . __CLASS__ . ' PHP class';
        if (!$fh) {
            return false;
        }
        $cdrec = '';    // central directory content
        $entries = 0;    // number of zipped files
        $cnt_sheets = count($this->sheets);
        if ($cnt_sheets === 0) {
            $this->addSheet([], 'No data');
            $cnt_sheets = 1;
        }
        foreach ($this->template as $cfilename => $template) {
            if ($cfilename === 'xl/_rels/workbook.xml.rels') {
                $s = '';
                for ($i = 0; $i < $cnt_sheets; $i++) {
                    $s .= '<Relationship Id="rId' . ($i + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"' .
                        ' Target="worksheets/sheet' . ($i + 1) . ".xml\"/>\r\n";
                }
                $s .= '<Relationship Id="rId' . ($cnt_sheets + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' . "\r\n";
                $s .= '<Relationship Id="rId' . ($cnt_sheets + 2) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>';

                $template = str_replace('{RELS}', $s, $template);
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            } elseif ($cfilename === 'xl/workbook.xml') {
                $s = '';
                foreach ($this->sheets as $k => $v) {
                    $s .= '<sheet name="' . self::esc($v['name']) . '" sheetId="' . ($k + 1) . '" r:id="rId' . ($k + 1) . '"/>';
                }
                $search = ['{SHEETS}', '{APP}'];
                $replace = [$s, self::esc($this->application)];
                $template = str_replace($search, $replace, $template);
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            } elseif ($cfilename === 'docProps/app.xml') {
                $search = ['{APP}', '{COMPANY}', '{MANAGER}'];
                $replace = [self::esc($this->application), self::esc($this->company), self::esc($this->manager)];
                $template = str_replace($search, $replace, $template);
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            } elseif ($cfilename === 'docProps/core.xml') {
                $search = ['{DATE}', '{AUTHOR}', '{TITLE}', '{SUBJECT}', '{KEYWORD}', '{DESCRIPTION}', '{CATEGORY}', '{LANGUAGE}', '{LAST_MODIFY_BY}'];
                $replace = [gmdate('Y-m-d\TH:i:s\Z'), self::esc($this->author), self::esc($this->title), self::esc($this->subject), self::esc($this->keywords), self::esc($this->description), self::esc($this->category), self::esc($this->language), self::esc($this->lastModifiedBy)];
                $template = str_replace($search, $replace, $template);
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            } elseif ($cfilename === 'xl/sharedStrings.xml') {
                $si_cnt = count($this->SI);
                if ($si_cnt) {
                    $si = [];
                    foreach ($this->SI as $s) {
                        $si[] = '<si>' . (preg_match('/^\s|\s$/', $s) ? '<t xml:space="preserve">' . $s . '</t>' : '<t>' . $s . '</t>') . '</si>';
                    }
                    $template = str_replace(['{CNT}', '{STRINGS}'], [$si_cnt, implode("\r\n", $si)], $template);
                    $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                    $entries++;
                }
            } elseif ($cfilename === 'xl/worksheets/sheet1.xml') {
                foreach ($this->sheets as $k => $v) {
                    $filename = 'xl/worksheets/sheet' . ($k + 1) . '.xml';
                    $xml = $this->_sheetToXML($k, $template);
                    $this->_writeEntry($fh, $cdrec, $filename, $xml);
                    $entries++;
                }
                $xml = null;
            } elseif ($cfilename === '[Content_Types].xml') {
                $TYPES = [];
                foreach ($this->sheets as $k => $v) {
                    $TYPES[] = '<Override PartName="/xl/worksheets/sheet' . ($k + 1) . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
                }
                $template = str_replace('{TYPES}', implode("\r\n", $TYPES), $template);
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            } elseif ($cfilename === 'xl/styles.xml') {
                $NF = $XF = $FONTS = $F_KEYS = $FILLS = $FL_KEYS = [];
                $BR = ['<border><left/><right/><top/><bottom/><diagonal/></border>'];
                $BR_KEYS = [0 => 0];
                foreach ($this->NF as $k => $v) {
                    $NF[] = '<numFmt numFmtId="' . $k . '" formatCode="' . htmlspecialchars($v, ENT_QUOTES) . '"/>';
                }
                foreach ($this->XF as $xf) {
                    $F_KEY = $xf[2] . '-' . $xf[4] . '-' . $xf[7];
                    if (isset($F_KEYS[$F_KEY])) {
                        $F_ID = $F_KEYS[$F_KEY];
                    } else {
                        $F_ID = $F_KEYS[$F_KEY] = count($FONTS);
                        $FONTS[] = '<font><name val="' . $this->defaultFont . '"/><family val="2"/>'
                            . ($xf[7] ? '<sz val="' . $xf[7] . '"/>' : '<sz val="' . $this->defaultFontSize . '"/>')
                            . ($xf[2] & self::F_BOLD ? '<b/>' : '')
                            . ($xf[2] & self::F_ITALIC ? '<i/>' : '')
                            . ($xf[2] & self::F_UNDERLINE ? '<u/>' : '')
                            . '</font>';
                    }
                    $FL_KEY = $xf[3] . '-' . $xf[5];
                    if (isset($FL_KEYS[$FL_KEY])) {
                        $FL_ID = $FL_KEYS[$FL_KEY];
                    } else {
                        $FL_ID = $FL_KEYS[$FL_KEY] = count($FILLS);
                        $FILLS[] = '<fill><patternFill patternType="'
                            . ($xf[3] === 0 ? 'none' : '')
                            . ($xf[3] & self::FL_SOLID ? 'solid' : '')
                            . ($xf[3] & self::FL_GRAY_125 ? 'gray125' : '')
                            . '" /></fill>';
                    }
                    $align = '';
                    if ($xf[1] & self::A_CENTER) $align .= ' horizontal="center"';
                    if ($xf[1] & self::A_RIGHT) $align .= ' horizontal="right"';

                    $XF[] = '<xf numFmtId="' . $xf[0] . '" fontId="' . $F_ID . '" fillId="' . $FL_ID . '" borderId="0" xfId="0"'
                        . ($align ? ' applyAlignment="1"><alignment' . $align . '/></xf>' : '/>');
                }
                array_unshift($NF, '<numFmts count="' . count($NF) . '">');
                $NF[] = '</numFmts>';
                array_unshift($XF, '<cellXfs count="' . count($XF) . '">');
                $XF[] = '</cellXfs>';
                array_unshift($FONTS, '<fonts count="' . count($FONTS) . '">');
                $FONTS[] = '</fonts>';
                array_unshift($FILLS, '<fills count="' . count($FILLS) . '">');
                $FILLS[] = '</fills>';
                array_unshift($BR, '<borders count="' . count($BR) . '">');
                $BR[] = '</borders>';

                $template = str_replace(
                    ['{NUMFMTS}', '{FONTS}', '{XF}', '{FILLS}', '{BORDERS}'],
                    [implode("\r\n", $NF), implode("\r\n", $FONTS), implode("\r\n", $XF), implode("\r\n", $FILLS), implode("\r\n", $BR)],
                    $template
                );
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            } else {
                $this->_writeEntry($fh, $cdrec, $cfilename, $template);
                $entries++;
            }
        }
        $before_cd = ftell($fh);
        fwrite($fh, $cdrec);
        fwrite($fh, $dirSignatureE);
        fwrite($fh, pack('v', 0));
        fwrite($fh, pack('v', 0));
        fwrite($fh, pack('v', $entries));
        fwrite($fh, pack('v', $entries));
        fwrite($fh, pack('V', mb_strlen($cdrec, '8bit')));
        fwrite($fh, pack('V', $before_cd));
        fwrite($fh, pack('v', mb_strlen($zipComments, '8bit')));
        fwrite($fh, $zipComments);
        return true;
    }

    protected function _writeEntry($fh, &$cdrec, $cfilename, $data)
    {
        $zipSignature = "\x50\x4b\x03\x04";
        $dirSignature = "\x50\x4b\x01\x02";
        $e = [];
        $e['uncsize'] = mb_strlen($data, '8bit');
        if ($e['uncsize'] < 256) {
            $e['comsize'] = $e['uncsize'];
            $e['vneeded'] = 10;
            $e['cmethod'] = 0;
            $zdata = $data;
        } else {
            $zdata = gzcompress($data);
            $zdata = substr(substr($zdata, 0, -4), 2);
            $e['comsize'] = mb_strlen($zdata, '8bit');
            $e['vneeded'] = 10;
            $e['cmethod'] = 8;
        }
        $e['bitflag'] = 0;
        $e['crc_32'] = crc32($data);
        $date = getdate();
        $e['dostime'] = ((($date['year'] - 1980) << 25) | ($date['mon'] << 21) | ($date['mday'] << 16) | ($date['hours'] << 11) | ($date['minutes'] << 5) | ($date['seconds'] >> 1));
        $e['offset'] = ftell($fh);
        fwrite($fh, $zipSignature);
        fwrite($fh, pack('v', $e['vneeded']));
        fwrite($fh, pack('v', $e['bitflag']));
        fwrite($fh, pack('v', $e['cmethod']));
        fwrite($fh, pack('V', $e['dostime']));
        fwrite($fh, pack('V', $e['crc_32']));
        fwrite($fh, pack('V', $e['comsize']));
        fwrite($fh, pack('V', $e['uncsize']));
        fwrite($fh, pack('v', mb_strlen($cfilename, '8bit')));
        fwrite($fh, pack('v', 0));
        fwrite($fh, $cfilename);
        fwrite($fh, $zdata);
        $e['external_attributes'] = 32;
        $e['comments'] = '';
        $cdrec .= $dirSignature;
        $cdrec .= "\x0\x0";
        $cdrec .= pack('v', $e['vneeded']);
        $cdrec .= "\x0\x0";
        $cdrec .= pack('v', $e['cmethod']);
        $cdrec .= pack('V', $e['dostime']);
        $cdrec .= pack('V', $e['crc_32']);
        $cdrec .= pack('V', $e['comsize']);
        $cdrec .= pack('V', $e['uncsize']);
        $cdrec .= pack('v', mb_strlen($cfilename, '8bit'));
        $cdrec .= pack('v', 0);
        $cdrec .= pack('v', mb_strlen($e['comments'], '8bit'));
        $cdrec .= pack('v', 0);
        $cdrec .= pack('v', 0);
        $cdrec .= pack('V', $e['external_attributes']);
        $cdrec .= pack('V', $e['offset']);
        $cdrec .= $cfilename;
        $cdrec .= $e['comments'];
    }

    protected function _sheetToXML($idx, $template)
    {
        $ROWS = [];
        $ROWS[] = '<sheetData>';
        $CUR_ROW = 0;
        foreach ($this->sheets[$idx]['rows'] as $r) {
            $CUR_ROW++;
            $row = '';
            $CUR_COL = 0;
            foreach ($r as $v) {
                $CUR_COL++;
                $cname = self::coord2cell($CUR_COL-1) . $CUR_ROW;
                if ($v === null || $v === '') {
                    $row .= '<c r="' . $cname . '"/>';
                    continue;
                }
                $ct = $cv = $cs = null;
                if (is_numeric($v)) {
                    $cv = $v;
                } else {
                    $ct = 's';
                    $v = self::esc((string)$v);
                    $skey = '~' . $v;
                    if (isset($this->SI_KEYS[$skey])) {
                        $cv = $this->SI_KEYS[$skey];
                    } else {
                        $this->SI[] = $v;
                        $cv = count($this->SI) - 1;
                        $this->SI_KEYS[$skey] = $cv;
                    }
                }
                $row .= '<c r="' . $cname . '"' . ($ct ? ' t="' . $ct . '"' : '') . ($cs ? ' s="' . $cs . '"' : '') . '><v>' . $cv . '</v></c>';
            }
            $ROWS[] = '<row r="' . $CUR_ROW . '">' . $row . "</row>";
        }
        $ROWS[] = '</sheetData>';
        $REF = 'A1:' . self::coord2cell(count($this->sheets[$idx]['rows'][0] ?? [])-1) . $CUR_ROW;
        return str_replace(['{REF}', '{ROWS}', '{SHEETVIEWS}', '{COLS}', '{AUTOFILTER}', '{MERGECELLS}', '{HYPERLINKS}', '{VML}'], [$REF, implode("\r\n", $ROWS), '', '', '', '', '', ''], $template);
    }

    public function setTitle($title) { $this->title = $title; return $this; }
    public static function esc($str) { return str_replace(['&', '<', '>', "\x00"], ['&amp;', '&lt;', '&gt;', ''], $str); }
    public static function coord2cell($x, $y = null) {
        $c = '';
        for ($i = $x; $i >= 0; $i = ((int)($i / 26)) - 1) $c = chr(65 + $i % 26) . $c;
        return $c . ($y === null ? '' : ($y + 1));
    }
}
