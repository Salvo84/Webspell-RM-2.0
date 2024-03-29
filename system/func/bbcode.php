<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

function unhtmlspecialchars($input)
{
    $input = preg_replace("/&gt;/i", ">", $input);
    $input = preg_replace("/&lt;/i", "<", $input);
    $input = preg_replace("/&quot;/i", "\"", $input);
    $input = preg_replace("/&amp;/i", "&", $input);

    return $input;
}

/*function replace_smileys($text, $calledfrom = 'root')
{
    if ($calledfrom == 'admin') {
        $prefix = '.';
        $prefix2 = '../';
    } else {
        $prefix = '';
        $prefix2 = '';
    }


    $replacements_1 = array();
    $replacements_2 = array();


    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "settings_smileys`");
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $replacements_1[] = $ds['pattern'];
        $replacements_2[] = '[SMILE=' . $ds['alt'] . ']  '. $ds['name']. ' [/SMILE]';
    }

    $text = strtr($text, array_combine($replacements_1, $replacements_2));

    return $text;
}

function smileys($text, $specialchars = 0, $calledfrom = 'root')
{

    if ($specialchars) {
        $text = unhtmlspecialchars($text);
    }
    $splits = preg_split("/(\[[\/]{0,1}code\])/si", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $anz = count($splits);
    for ($i = 0; $i < $anz; $i++) {
        $opentags = 0;
        $closetags = 0;
        $match = false;
        if (strtolower($splits[$i]) == "[code]") {
            $opentags++;
            for ($z = ($i + 1); $z < $anz; $z++) {
                if (strtolower($splits[$z]) == "[code]") {
                    $opentags++;
                }
                if (strtolower($splits[$z]) == "[/code]") {
                    $closetags++;
                }
                if ($closetags == $opentags) {
                    $match = true;
                    break;
                }
            }
        }
        if ($match === false) {
            $splits[$i] = replace_smileys($splits[$i], $calledfrom);
        } else {
            $i = $z;
        }
    }
    $text = implode("", $splits);
    if ($specialchars) {
        $text = htmlspecialchars($text);
    }
    return $text;
}*/

function htmlnl($text)
{
    preg_match_all(
        // @codingStandardsIgnoreStart
        '/<(table|form|li|ul|ol|tr|td|dl|dt|dd|dir|menu|th|thead|caption|colgroup|col|tbody|tfoot|div|span*)[^>]*>(.*?)<\/\1>/si',
        // @codingStandardsIgnoreEnd
        $text,
        $matches,
        PREG_SET_ORDER
    );
    foreach ($matches as $match) {
        if (stristr($match[0], 'class="quote"') === false &&
            stristr($match[0], 'class="code"') === false &&
            stristr($match[0], 'align=') === false &&
            stristr($match[0], 'size=') === false &&
            stristr($match[0], 'color=') === false
        ) {
            $new_str = str_replace(array("\r\n", "\n", "\r"), array("", "", ""), $match[0]);
            $text = str_replace($match[0], $new_str, $text);
        }
    }
    return $text;
}

function fixJavaEvents($string)
{
    return str_replace(array(
        'onabort=',
        'onblur=',
        'onchange=',
        'onclick=',
        'ondblclick=',
        'onerror=',
        'onfocus=',
        'onkeydown=',
        'onkeypress=',
        'onkeyup=',
        'onload=',
        'onmousedown=',
        'onmousemove=',
        'onmouseout=',
        'onmouseover=',
        'onmouseup=',
        'onreset=',
        'onresize=',
        'onselect=',
        'onsubmit=',
        'onunload=',
        ' '
    ), '', $string);
}

function flags($text, $calledfrom = 'root')
{
    global $_language;

    $prefix = '';
    if ($calledfrom == 'admin') {
        $prefix = '../';
        $_language->readModule('bbcode', true, true);
    } else {
        $_language->readModule('bbcode', true, false);
    }

    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "settings_countries`");
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $text = str_ireplace(
            "[flag]" . $ds['short'] . "[/flag]",
            '<img src="' . $prefix . 'images/flags/' . $ds['short'] . '.gif" alt="' . $ds['country'] . '" />',
            $text
        );
    }

    $text = str_ireplace(
        "[flag][/flag]",
        '<img src="' . $prefix . 'images/flags/unknown.gif" alt="' . $_language->module['na'] . '" />',
        $text
    );
    $text = str_ireplace("[flag]", '', $text);
    $text = str_ireplace("[/flag]", '', $text);

    return $text;
}

//replace [code]-tags

function codereplace($content)
{
    global $_language;
    $_language->readModule('bbcode', true);

    global $picsize_l;

    $splits = preg_split("/(\[[\/]{0,1}code\])/si", $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    $anz = count($splits);
    for ($i = 0; $i < $anz; $i++) {
        $opentags = 0;
        $closetags = 0;
        $match = false;
        if (strtolower($splits[$i]) == "[code]") {
            $opentags++;
            for ($z = ($i + 1); $z < $anz; $z++) {
                if (strtolower($splits[$z]) == "[code]") {
                    $opentags++;
                } elseif (strtolower($splits[$z]) == "[/code]") {
                    $closetags++;
                }
                if ($closetags == $opentags) {
                    $match = true;
                    break;
                }
            }
            if ($match) {
                $splits[$i] =
                    '<div style="max-width:' . $picsize_l . 'px;" class="panel panel-default code">' .
                    '<div class="panel-heading">' . $_language->module['code'] . ':</div><div class="panel-body">';

                /* concat pieces until arriving closing tag ($z) and save to $i+1 */
                for ($x = ($i + 2); $x < $z; $x++) {
                    $splits[($i + 1)] .= $splits[$x];
                    unset($splits[$x]);
                }

                $splits[($i + 1)] = insideCode($splits[($i + 1)]);
                $splits[$z] = '</div></div>';
                $i = $z;
            }
        }
    }
    $content = implode($splits);
    return $content;
}

//replace inside [code]-tags
function insideCode($content)
{

    global $userID;
    $code_entities_match = array(
        '#"#',
        '#<#',
        '#>#',
        '#:#',
        '#\[#',
        '#\]#',
        '#\(#',
        '#\)#',
        '#\{#',
        '#\}#',
        '#\t#',
        '#\040#'
    );
    $code_entities_replace = array(
        '&quot;',
        '&lt;',
        '&gt;',
        '&#58;',
        '&#91;',
        '&#93;',
        '&#40;',
        '&#41;',
        '&#123;',
        '&#125;',
        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        '&nbsp;'
    );

    $content = preg_replace($code_entities_match, $code_entities_replace, $content);

    // add line number
    $splits = preg_split("#\\n#", $content, -1, PREG_SPLIT_NO_EMPTY);

    $i = 0;
    $codecontent = '';
    foreach ($splits as $res) {
        if ($i > 0 || trim($res) != "") {
            $codecontent .= '<li>' . $res . '</li>';
            $i++;
        }
    }
    $content = '<ol>' . $codecontent . '</ol>';

    return $content;
}

//replace [img]-tags

function imgreplace_callback($match)
{
    return '<img src="'.fixJavaEvents($match[1].$match[2]).'" border="0"'.
            'alt="'.fixJavaEvents($match[1].$match[2]).'" />';
}

function imgreplace($content)
{
    global $_language;
    $_language->readModule('bbcode', true);

    global $picsize_l;
    global $picsize_h;
    global $autoresize;

    $content = preg_replace("#\[/img\]([\n\r]*)#si", "[/img]", $content);

    if ($autoresize > 0) {
        preg_match_all("#(\[img\])(.*?)(png|gif|jpeg|jpg)(\[\/img\])#i", $content, $imgtags, PREG_SET_ORDER);
        $i = 0;
        foreach ($imgtags as $teil) {
            $teil[2] .= $teil[3];
            $i++;
            if ($autoresize == 1) {

				if(!empty($teil[2])) {
					if(@getimagesize($teil[2])) {
						$picinfo = getimagesize($teil[2]);
					} else {
						$err = "{".$_language->module['na']."}";
					}
				} else {
					$err = "{".$_language->module['na']."}";
				}
                $format = "unknown";
                switch ($picinfo[2]) {
                    case 1:
                        $format = "gif";
                        break;
                    case 2:
                        $format = "jpeg";
                        break;
                    case 3:
                        $format = "png";
                        break;
                }
                if (!$picsize_l) {
                    $size_l = "9999";
                } else {
                    $size_l = $picsize_l;
                }
                if (!$picsize_h) {
                    $size_h = "9999";
                } else {
                    $size_h = $picsize_h;
                }
				#if(!isset($err)) {
					if ($picinfo[0] > $size_l || $picinfo[1] > $size_h) {
						if(isset($err)) {
							$nfo = $err;
						} else {
							$nfo = '[i]' . $_language->module['auto_resize'] .
							': ' . $picinfo[1] . 'x' . $picinfo[0] . 'px, ' . $format . '[/i]';
						}
						$content = str_ireplace(
							'[img]' . $teil[2] . '[/img]',
							'[url=' . $teil[2] . ']' .
							'<div style="width: 100%;"><img src="' . fixJavaEvents($teil[2]) . '" width="' . $picsize_l .
							'" alt="' . $teil[2] . '" /><br />('.$nfo.')[/url]</div>',
							$content
						);
					} elseif ($picinfo[0] > (2 * $size_l) || $picinfo[1] > (2 * $size_h)) {
						if(isset($err)) {
							$nfo = $err;
						} else {
							$nfo = $picinfo[1] . 'x' . $picinfo[0] . 'px, ' . $format;
						}
						$content = str_ireplace(
							'<div style="width: 100%;">[img]' . $teil[2] . '[/img]',
							'[url=' . $teil[2] . '][b]' . $_language->module['large_picture'] .
							'[/b]<br />(' . $nfo . ')[/url]</div>',
							$content
						);
					} else {
						$content = preg_replace(
							'#\[img\]' . preg_quote($teil[2], "#") . '\[/img\]#si',
							'<div style="width: 100%;"><img ' .
							'class="img-responsive"'.
							'src="' . fixJavaEvents($teil[2]) . '" ' .
							'alt="' . $teil[2] . '" ' .
							'/></div>',
							$content,
							1
						);
					}
				#}
            } else {
                $n = str_replace('.', '', microtime(1)) . '_' . $i;
                $n = str_replace(' ', '', $n);
                $content = preg_replace(
                    '#\[img\]' . preg_quote($teil[2], "#") . '\[/img\]#si',
                    '<div ' .
                    'id="ws_imagediv_' . $n . '" ' .
                    'style="display:none;">' .
                    '<img ' .
                    'src="' . fixJavaEvents($teil[2]) . '" ' .
                    'id="ws_image_' . $n . '" ' .
                    'onload="checkSize(\'' . $n . '\', ' . $picsize_l . ', ' . $picsize_h . ')" ' .
                    'alt="' . fixJavaEvents($teil[2]) . '" ' .
                    'style="max-width: ' . ($picsize_l + 1) . 'px; max-height: ' . ($picsize_h + 1) . 'px;" ' .
                    '/><br>' .
                    '[url=' . fixJavaEvents($teil[2]) . ']' .
                    '[i]' .
                    '(' . $_language->module['auto_resize'] . ': ' .
                    $_language->module['show_original'] . ')' .
                    '[/i]' .
                    '</div>'.
                    '[/url]',
                    $content,
                    1
                );
            }
        }
    } else {
        $content = preg_replace_callback(
            "#\[img\](.*?)(png|gif|jpeg|jpg)\[/img\]#si",
            "imgreplace_callback",
            $content
        );
    }

    return $content;
}

function youtubereplace($content) {
	$content = preg_replace("/\[youtube\](?:http?:\/\/)?(?:https?:\/\/)(?:www\.)?youtu(?:\.be\/|be\.com\/watch\?v=)([A-Z0-9\-_]+)(?:&(.*?))?\[\/youtube\]/si", "<iframe class=\"youtube-player\" type=\"text/html\" width=\"640\" height=\"385\" src=\"//www.youtube.com/embed/\\1\" frameborder=\"0\"></iframe>", $content);
	$content = preg_replace("/\[youtube\]([A-Z0-9\-_]+)(?:&(.*?))?\[\/youtube\]/si", "<iframe class=\"youtube-player\" type=\"text/html\" width=\"640\" height=\"385\" src=\"//www.youtube.com/embed/\\1\" frameborder=\"0\"></iframe>" ,$content);
	return $content;
	
}

function gistreplace($content) {
	$content = preg_replace("/\[gist\](.*?)\[\/gist\]/si", "<script src=\"https://gist.github.com/\\1.js\"></script>", $content);
	return $content;
	
}

//replace [quote]-tags

function quotereplace($content)
{

    global $_language, $picsize_l, $picsize_h;
    $_language->readModule('bbcode', true);
    
    $content = preg_replace("#\[/quote\]([\n\r]*)#si", "[/quote]", $content);

    $content = str_ireplace('[quote]', '[quote]', $content);
    $content = str_ireplace('[/quote]', '[/quote]', $content);
    $wrote = $_language->module['wrote'];

    //prepare: how often start- and end-tag occurrs
    $starttags = substr_count($content, '[quote]') + preg_match_all("#\[quote=(.*?)\]#si", $content, $matches);
    $endtags = substr_count($content, '[/quote]');

    $overflow = abs($starttags - $endtags);

    for ($i = 0; $i < $overflow; $i++) {
        if ($starttags > $endtags) {
            $content = $content . '[/quote]';
        } elseif ($endtags > $starttags) {
            $content = '[quote]' . $content;
        }
    }

    $content = preg_replace(
        "#\[quote=(.*?)\]#si",
        '<blockquote style="max-width:' . $picsize_l . 'px;" class="quote"><header>\\1 ' . $wrote . ':</header>',
        $content,
        10
    );
    $content = preg_replace(
        "#\[quote\]#s",
        '<blockquote style="max-width:' . $picsize_l . 'px;" class="quote">',
        $content,
        10
    );
    $content = preg_replace("#\[/quote\]#s", '</blockquote>', $content, 20);

    //remove overflowed quote-tags

    $content = preg_replace("#\[quote=(.*?)\]#si", "", $content);
    $content = str_replace('[quote]', '', $content);
    $content = str_replace('[/quote]', '', $content);

    return $content;

}

function cut_middle($str, $max = 50)
{
    $strlen = mb_strlen($str);
    if ($strlen > $max) {
        $part1 = mb_substr($str, 0, $strlen / 2);
        $part2 = mb_substr($str, $strlen / 2);
        $part1 = mb_substr($part1, 0, ($max / 2) - 3) . "...";
        $part2 = mb_substr($part2, -($max / 2));
        $str = $part1 . $part2;
    }
    return $str;
}


function urlreplace_callback($match)
{
    $parsed = parse_url($match[1]);
    if (!isset($parsed['host'])) {
        if (!file_exists($parsed['path'])) {
            $url = "http://".$match[1];
        } else {
            $url = $match[1];
        }
    } elseif (!isset($parsed['scheme'])) {
        $url = "http://".$match[1];
    } else {
        $url = $match[1];
    }
    return '<a href="'.fixJavaEvents($url).'" target="_blank">'.$match[2].'</a>';
}


function urlreplace($content)
{
    $content = preg_replace("#\[url\](.*?)\[/url\]#i", "[url=\\1]\\1[/url]", $content);
    $content = preg_replace_callback("#\[url=([^\]]*?)\](.*?)\[/url\]#si", "urlreplace_callback", $content);
    return $content;
}

function linkreplace($link)
{
    if (ord($link[1]) == 39 || ord($link[1]) == 62) {
        return $link[0];
    } else {
        $backup = "";
        $backup_end = "";
        if (mb_substr($link[0], -1, 1) == "]") {
            $backup = mb_substr($link[0], 0, 1);
            $link[0] = mb_substr($link[0], 1);
            $link[0] = mb_substr($link[0], 0, mb_strrpos($link[0], "["));
            $backup_end = mb_substr($link[3], mb_strrpos($link[3], "["));
            $link[3] = mb_substr($link[3], 0, mb_strrpos($link[3], "["));
        }
        $check = preg_match(
            "%(http://|https://|ftp://|mailto:|news:|www\.)([a-zA-Z0-9-\.]{3,50})%si",
            $link[0]
        );
        if ($check) {
            $http = $link[2];
            if (mb_substr($http, 0, 4) == "www.") {
                $http = "http://" . $http;
            }
            $link = str_replace(
                trim($link[0]),
                '<a href="' . $http . $link[3] . '" target="_blank" rel="nofollow">' . $link[2] . $link[3] . '</a>',
                $link[0]
            );
            return $backup . $link . $backup_end;
        }
        return $backup . $link[0] . $backup_end;
    }
}

//insert member links
function insertlinks($content, $calledfrom = 'root')
{
    global $insertlinks;
    if ($calledfrom == 'admin') {
        $prefix = '../';
    } else {
        $prefix = '';
    }

    if ($insertlinks == 1) {
        $ergebnis = safe_query(
            "SELECT
                us.userID,
                us.nickname,
                us.country
            FROM
                `" . PREFIX . "squads_members` AS sq,
                `" . PREFIX . "user` AS us
            WHERE
                sq.userID = us.userID
            GROUP BY
                us.userID"
        );
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $content = str_replace(
                $ds['nickname'] . ' ',
                '[flag]' . $ds['country'] . '[/flag] ' .
                '<a ' .
                'href="' . $prefix . 'index.php?site=profile&amp;id=' . $ds['userID'] .
                '">' .
                $ds['nickname'] .
                '</a>&nbsp;',
                $content
            );
        }
        return $content;
    } else {
        return $content;
    }
}

function cut_urls($link)
{
    $new_str = $link[1];
    if (!stristr($link[1], "<img") && !stristr($link[1], "[SMILE")) {
        $new_str = cut_middle($link[1]);
    }
    $link[0] = (stristr($link[0], "javascript:")) ? str_ireplace("javascript:", "#killed", $link[0]) : $link[0];
    return str_replace(">" . $link[1], ">" . $new_str, $link[0]);
}

function removeIllegalCharacerts($string)
{
    return preg_replace("/[^a-z0-9#]/si", "", $string);
}

function removeIllegalCharacertsWithoutUrls($string)
{
    return preg_replace("/[^a-z0-9#\/\.]/si", "", $string);
}

function emailreplace_callback_1($match)
{
    return '<a href="mailto:' . mail_protect(fixJavaEvents($match[1])) . '">' . fixJavaEvents($match[1]) . '</a>';
}

function emailreplace_callback_2($match)
{
    return '<a href="mailto:' . mail_protect(fixJavaEvents($match[1])) . '">' . $match[2] . '</a>';
}

function font_size_callback($match)
{
    return '<font size="' . removeIllegalCharacerts($match[1]) . '">' . $match[2] . '</font>';
}

function font_color_callback($match)
{
    return '<font color="' . removeIllegalCharacerts($match[1]) . '">' . $match[2] . '</font>';
}

function font_face_callback($match)
{
    return '<font face="' . removeIllegalCharacerts($match[1]) . '">' . $match[2] . '</font>';
}

function align_callback($match)
{
    return '<div align="' . removeIllegalCharacerts($match[1]) . '">' . $match[2] . '</div>';
}

function smiley_callback($match)
{
    return
        '<img ' .
            'src="' . removeIllegalCharacertsWithoutUrls($match[2]) . '" ' .
            'alt="' . removeIllegalCharacerts($match[1]) . '" />';
}

function replacement($content, $bbcode = true)
{
        if ($bbcode) {
        $content = codereplace($content);
        $content = imgreplace($content);
        $content = quotereplace($content);
        $content = urlreplace($content);
        $content = youtubereplace($content);
        $content = gistreplace($content);
        $content = preg_replace_callback(
            "#(^|<[^\"=]{1}>|\s|\[b|i|u\]][^<a.*>])(http://|https://|ftp://|mailto:|news:|www.)([^\s<>|$]+)#si",
            "linkreplace",
            $content
        );
        $content = preg_replace_callback("#\[email\](.*?)\[/email\]#si", "emailreplace_callback_1", $content);
        $content = preg_replace_callback("#\[email=(.*?)\](.*?)\[/email\]#si", "emailreplace_callback_2", $content);
        $content = preg_replace_callback("#<a\b[^>]*>(.*?)</a>#si", "cut_urls", $content);
        while (preg_match("#\[size=([0-9]*)\](.*?)\[/size\]#si", $content)) {
            $content = preg_replace_callback("#\[size=([0-9]*)\](.*?)\[/size\]#si", "font_size_callback", $content);
        }
        while (preg_match("#\[color=([a-z0-9\#]*)\](.*?)\[/color\]#si", $content)) {
            $content = preg_replace_callback(
                "#\[color=([a-z0-9\#]*)\](.*?)\[/color\]#si",
                "font_color_callback",
                $content
            );
        }
        while (preg_match("#\[font=([a-z0-9]*)\](.*?)\[/font\]#si", $content)) {
            $content = preg_replace_callback("#\[font=([a-z0-9]*)\](.*?)\[/font\]#si", "font_face_callback", $content);
        }
        while (preg_match("#\[align=([a-z0-9]*)\](.*?)\[/align\]#si", $content)) {
            $content = preg_replace_callback("#\[align=([a-z0-9]*)\](.*?)\[/align\]#si", "align_callback", $content);
        }
        $content = preg_replace("#\[b\](.*?)\[/b\]#si", "<b>\\1</b>", $content);
        $content = preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>", $content);
        $content = preg_replace("#\[u\](.*?)\[/u\]#si", "<span class='underline'>\\1</span>", $content);
        $content = preg_replace("#\[s\](.*?)\[/s\]#si", "<s>\\1</s>", $content);
        $content = preg_replace("#\[list\][\s]{0,}(.*?)\[/list\]#si", "<ul class='list'>\\1</ul>", $content);
        $content = preg_replace("#\[list=1\][\s]{0,}(.*?)\[/list=1\]#si", "<ol class='list_num'>\\1</ol>", $content);
        $content = preg_replace(
            "#\[list=a\][\s]{0,}(.*?)\[/list=a\]#si",
            "<ol type=\"a\" class='list_alpha'>\\1</ol>",
            $content
        );
        $content = preg_replace("#\[\*\](.*?)\[/\*\](\s){0,}#si", "<li>\\1</li>", $content);
        $content = preg_replace("#\[br]#si", "<br />", $content);
        $content = preg_replace("#\[hr]#si", "<hr />", $content);
        $content = preg_replace("#\[center]#si", "<p class=\"text-center\">", $content);
        $content = preg_replace("#\[/center]#si", "</p>", $content);
        
    }
    $content = preg_replace_callback("#\[SMILE=(.*?)\](.*?)\[/SMILE\]#si", "smiley_callback", $content);

    return $content;
}

function toggle($content, $id)
{
    global $_language;
    $_language->readModule('bbcode', true);
    $replace1 = '<div style="width: 100%;">
                    <a class="btn btn-default" role="button" data-toggle="collapse" href="#ToggleRow_' . $id . '_%d" aria-expanded="false" aria-controls="collapseRow">
                        ' . $_language->module['read_more'] . '             
                    </a>
                    <div class="collapse" id="ToggleRow_' . $id . '_%d">';
    $replace2 = '</div></div>';


    $n = 0;
    while (($pos = mb_strpos(strtolower($content), "[toggle=")) !== false) {
        $start = mb_substr($content, 0, $pos);
        $end = mb_substr($content, $pos);

        $toggle_name_end = mb_strpos($end, "]");

        if (($toggle_close_tag = mb_strpos(strtolower($end), "[/toggle]")) === false) {
            $content = $start . mb_substr($end, $toggle_name_end + 1);
        } else {
            $toggle_name = mb_substr($end, 8, $toggle_name_end - 8);
            $middle = str_replace("%d", $n, str_replace("%s", $toggle_name, $replace1));
            $toggle_content = mb_substr($end, $toggle_name_end + 1, $toggle_close_tag - $toggle_name_end - 1);
            $end = mb_substr($content, $pos + $toggle_close_tag + 9);
            $content = $start . $middle . $toggle_content . $replace2 . $end;
            $n++;
        }
    }

    $content = str_ireplace("[/toggle]", "", $content);

    return $content;
}
