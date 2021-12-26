<?php
// CubicleSoft PHP UTF (Unicode) utility functions.
// (C) 2021 CubicleSoft.  All Rights Reserved.

class UTFUtils
{
    const UTF8 = 1;
    const UTF8_BOM = 2;
    const UTF16_LE = 3;
    const UTF16_BE = 4;
    const UTF16_BOM = 5;
    const UTF32_LE = 6;
    const UTF32_BE = 7;
    const UTF32_BOM = 8;
    const UTF32_ARRAY = 9;

    // Checks a numeric value to see if it is a combining code point.
    public static function IsCombiningCodePoint($val)
    {
        return ($val >= 0x0300 && $val <= 0x036f) ||
            ($val >= 0x1dc0 && $val <= 0x1dff) ||
            ($val >= 0x20d0 && $val <= 0x20ff) ||
            ($val >= 0xfe20 && $val <= 0xfe2f);
    }

    public static function Convert($data, $srctype, $desttype)
    {
        $arr = is_array($data);
        if ($arr) {
            $srctype = self::UTF32_ARRAY;
        }
        $x = 0;
        $y = $arr ? count($data) : strlen($data);
        $result = $desttype === self::UTF32_ARRAY ? [] : "";
        if (!$arr && $srctype === self::UTF32_ARRAY) {
            return $result;
        }

        $first = true;

        if ($srctype === self::UTF8_BOM) {
            if (substr($data, 0, 3) === "\xEF\xBB\xBF") {
                $x = 3;
            }

            $srctype = self::UTF8;
        }

        if ($srctype === self::UTF16_BOM) {
            if (substr($data, 0, 2) === "\xFE\xFF") {
                $srctype = self::UTF16_BE;
                $x = 2;
            } elseif (substr($data, 0, 2) === "\xFF\xFE") {
                $srctype = self::UTF16_LE;
                $x = 2;
            } else {
                $srctype = self::UTF16_LE;
            }
        }

        if ($srctype === self::UTF32_BOM) {
            if (substr($data, 0, 4) === "\x00\x00\xFE\xFF") {
                $srctype = self::UTF32_BE;
                $x = 4;
            } elseif (substr($data, 0, 4) === "\xFF\xFE\x00\x00") {
                $srctype = self::UTF32_LE;
                $x = 4;
            } else {
                $srctype = self::UTF32_LE;
            }
        }

        while ($x < $y) {
            // Read the next valid code point.
            $val = false;

            switch ($srctype) {
                case self::UTF8:
                    $tempchr = ord($data[$x]);
                    if ($tempchr <= 0x7f) {
                        $val = $tempchr;
                        $x++;
                    } elseif ($tempchr < 0xc2) {
                        $x++;
                    } else {
                        $left = $y - $x;
                        if ($left < 2) {
                            $x++;
                        } else {
                            $tempchr2 = ord($data[$x + 1]);

                            if (
                                $tempchr >= 0xc2 &&
                                $tempchr <= 0xdf &&
                                ($tempchr2 >= 0x80 && $tempchr2 <= 0xbf)
                            ) {
                                $val =
                                    (($tempchr & 0x1f) << 6) |
                                    ($tempchr2 & 0x3f);
                                $x += 2;
                            } elseif ($left < 3) {
                                $x++;
                            } else {
                                $tempchr3 = ord($data[$x + 2]);

                                if ($tempchr3 < 0x80 || $tempchr3 > 0xbf) {
                                    $x++;
                                } else {
                                    if (
                                        ($tempchr == 0xe0 &&
                                            ($tempchr2 >= 0xa0 &&
                                                $tempchr2 <= 0xbf)) ||
                                        ((($tempchr >= 0xe1 &&
                                            $tempchr <= 0xec) ||
                                            $tempchr == 0xee ||
                                            $tempchr == 0xef) &&
                                            ($tempchr2 >= 0x80 &&
                                                $tempchr2 <= 0xbf)) ||
                                        ($tempchr == 0xed &&
                                            ($tempchr2 >= 0x80 &&
                                                $tempchr2 <= 0x9f))
                                    ) {
                                        $val =
                                            (($tempchr & 0x0f) << 12) |
                                            (($tempchr2 & 0x3f) << 6) |
                                            ($tempchr3 & 0x3f);
                                        $x += 3;
                                    } elseif ($left < 4) {
                                        $x++;
                                    } else {
                                        $tempchr4 = ord($data[$x + 3]);

                                        if (
                                            $tempchr4 < 0x80 ||
                                            $tempchr4 > 0xbf
                                        ) {
                                            $x++;
                                        } elseif (
                                            ($tempchr == 0xf0 &&
                                                ($tempchr2 >= 0x90 &&
                                                    $tempchr2 <= 0xbf)) ||
                                            ($tempchr >= 0xf1 &&
                                                $tempchr <= 0xf3 &&
                                                ($tempchr2 >= 0x80 &&
                                                    $tempchr2 <= 0xbf)) ||
                                            ($tempchr == 0xf4 &&
                                                ($tempchr2 >= 0x80 &&
                                                    $tempchr2 <= 0x8f))
                                        ) {
                                            $val =
                                                (($tempchr & 0x07) << 18) |
                                                (($tempchr2 & 0x3f) << 12) |
                                                (($tempchr3 & 0x3f) << 6) |
                                                ($tempchr4 & 0x3f);
                                            $x += 4;
                                        } else {
                                            $x++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    break;
                case self::UTF16_LE:
                    if ($x + 1 >= $y) {
                        $x = $y;
                    } else {
                        $val = unpack("v", substr($data, $x, 2))[1];
                        $x += 2;

                        if ($val >= 0xd800 && $val <= 0xdbff) {
                            if ($x + 1 >= $y) {
                                $x = $y;
                                $val = false;
                            } else {
                                $val2 = unpack("v", substr($data, $x, 2))[1];

                                if ($val2 < 0xdc00 || $val2 > 0xdfff) {
                                    $val = false;
                                } else {
                                    $val =
                                        (($val - 0xd800 << 10) |
                                            ($val2 - 0xdc00)) +
                                        0x10000;
                                    $x += 2;
                                }
                            }
                        }
                    }

                    break;
                case self::UTF16_BE:
                    if ($x + 1 >= $y) {
                        $x = $y;
                    } else {
                        $val = unpack("n", substr($data, $x, 2))[1];
                        $x += 2;

                        if ($val >= 0xd800 && $val <= 0xdbff) {
                            if ($x + 1 >= $y) {
                                $x = $y;
                                $val = false;
                            } else {
                                $val2 = unpack("n", substr($data, $x, 2))[1];

                                if ($val2 < 0xdc00 || $val2 > 0xdfff) {
                                    $val = false;
                                } else {
                                    $val =
                                        (($val - 0xd800 << 10) |
                                            ($val2 - 0xdc00)) +
                                        0x10000;
                                    $x += 2;
                                }
                            }
                        }
                    }

                    break;
                case self::UTF32_LE:
                    if ($x + 3 >= $y) {
                        $x = $y;
                    } else {
                        $val = unpack("V", substr($data, $x, 4))[1];
                        $x += 4;
                    }

                    break;
                case self::UTF32_BE:
                    if ($x + 3 >= $y) {
                        $x = $y;
                    } else {
                        $val = unpack("N", substr($data, $x, 4))[1];
                        $x += 4;
                    }

                    break;
                case self::UTF32_ARRAY:
                    $val = (int) $data[$x];
                    $x++;

                    break;
                default:
                    $x = $y;
                    break;
            }

            // Make sure it is a valid Unicode value.
            // 0xD800-0xDFFF are for UTF-16 surrogate pairs.  Invalid characters.
            // 0xFDD0-0xFDEF are non-characters.
            // 0x*FFFE and 0x*FFFF are reserved.
            // The largest possible character is 0x10FFFF.
            // First character can't be a combining code point.
            if (
                $val !== false &&
                !(
                    $val < 0 ||
                    ($val >= 0xd800 && $val <= 0xdfff) ||
                    ($val >= 0xfdd0 && $val <= 0xfdef) ||
                    ($val & 0xfffe) == 0xfffe ||
                    $val > 0x10ffff ||
                    ($first && self::IsCombiningCodePoint($val))
                )
            ) {
                if ($first) {
                    if ($desttype === self::UTF8_BOM) {
                        $result .= "\xEF\xBB\xBF";

                        $desttype = self::UTF8;
                    }

                    if ($desttype === self::UTF16_BOM) {
                        $result .= "\xFF\xFE";

                        $desttype = self::UTF16_LE;
                    }

                    if ($srctype === self::UTF32_BOM) {
                        $result .= "\xFF\xFE\x00\x00";

                        $desttype = self::UTF32_LE;
                    }

                    $first = false;
                }

                switch ($desttype) {
                    case self::UTF8:
                        if ($val <= 0x7f) {
                            $result .= chr($val);
                        } elseif ($val <= 0x7ff) {
                            $result .=
                                chr(0xc0 | ($val >> 6)) .
                                chr(0x80 | ($val & 0x3f));
                        } elseif ($val <= 0xffff) {
                            $result .=
                                chr(0xe0 | ($val >> 12)) .
                                chr(0x80 | (($val >> 6) & 0x3f)) .
                                chr(0x80 | ($val & 0x3f));
                        } elseif ($val <= 0x10ffff) {
                            $result .=
                                chr(0xf0 | ($val >> 18)) .
                                chr(0x80 | (($val >> 12) & 0x3f)) .
                                chr(0x80 | (($val >> 6) & 0x3f)) .
                                chr(0x80 | ($val & 0x3f));
                        }

                        break;
                    case self::UTF16_LE:
                        if ($val <= 0xffff) {
                            $result .= pack("v", $val);
                        } else {
                            $val -= 0x10000;
                            $result .= pack(
                                "v",
                                (($val >> 10) & 0x3ff) + 0xd800
                            );
                            $result .= pack("v", ($val & 0x3ff) + 0xdc00);
                        }

                        break;
                    case self::UTF16_BE:
                        if ($val <= 0xffff) {
                            $result .= pack("n", $val);
                        } else {
                            $val -= 0x10000;
                            $result .= pack(
                                "n",
                                (($val >> 10) & 0x3ff) + 0xd800
                            );
                            $result .= pack("n", ($val & 0x3ff) + 0xdc00);
                        }

                        break;
                    case self::UTF32_LE:
                        $result .= pack("V", $val);

                        break;
                    case self::UTF32_BE:
                        $result .= pack("N", $val);

                        break;
                    case self::UTF32_ARRAY:
                        $result[] = $val;

                        break;
                    default:
                        $x = $y;
                        break;
                }
            }
        }

        return $result;
    }

    protected const PUNYCODE_BASE = 36;
    protected const PUNYCODE_TMIN = 1;
    protected const PUNYCODE_TMAX = 26;
    protected const PUNYCODE_SKEW = 38;
    protected const PUNYCODE_DAMP = 700;
    protected const PUNYCODE_INITIAL_BIAS = 72;
    protected const PUNYCODE_INITIAL_N = 0x80;
    protected const PUNYCODE_DIGIT_MAP = "abcdefghijklmnopqrstuvwxyz0123456789";

    public static function ConvertToPunycode($domain)
    {
        // Reject invalid domain name lengths.
        if (strlen($domain) > 255) {
            return false;
        }

        $parts = explode(".", $domain);

        foreach ($parts as $num => $part) {
            // Reject invalid label lengths.
            $y = strlen($part);
            if ($y > 63) {
                return false;
            }

            // Skip already encoded portions.
            if (substr($part, 0, 4) === "xn--") {
                continue;
            }

            // Convert UTF-8 to UTF-32 code points.
            $data = self::Convert($part, self::UTF8, self::UTF32_ARRAY);

            // Handle ASCII code points.
            $part2 = "";
            foreach ($data as $cp) {
                if ($cp <= 0x7f) {
                    $part2 .= strtolower(chr($cp));
                }
            }

            $numhandled = strlen($part2);
            $y = count($data);

            if ($numhandled >= $y) {
                $parts[$num] = $part2;

                continue;
            }

            if ($numhandled) {
                $part2 .= "-";
            }

            $part2 = "xn--" . $part2;

            if (strlen($part2) > 63) {
                return false;
            }

            $bias = self::PUNYCODE_INITIAL_BIAS;
            $n = self::PUNYCODE_INITIAL_N;
            $delta = 0;
            $first = true;

            while ($numhandled < $y) {
                // Find the next largest unhandled code point.
                $cp2 = 0x01000000;
                foreach ($data as $cp) {
                    if ($cp >= $n && $cp2 > $cp) {
                        $cp2 = $cp;
                    }
                }

                // Increase delta but prevent overflow.
                $delta += ($cp2 - $n) * ($numhandled + 1);
                if ($delta < 0) {
                    return false;
                }
                $n = $cp2;

                foreach ($data as $cp) {
                    if ($cp < $n) {
                        $delta++;

                        if ($delta < 0) {
                            return false;
                        }
                    } elseif ($cp === $n) {
                        // Calculate and encode a variable length integer from the delta.
                        $q = $delta;
                        $x = 0;
                        do {
                            $x += self::PUNYCODE_BASE;

                            if ($x <= $bias) {
                                $t = self::PUNYCODE_TMIN;
                            } elseif ($x >= $bias + self::PUNYCODE_TMAX) {
                                $t = self::PUNYCODE_TMAX;
                            } else {
                                $t = $x - $bias;
                            }

                            if ($q < $t) {
                                break;
                            }

                            $part2 .=
                                self::PUNYCODE_DIGIT_MAP[
                                    $t +
                                        (($q - $t) % (self::PUNYCODE_BASE - $t))
                                ];

                            $q = (int) (($q - $t) / (self::PUNYCODE_BASE - $t));

                            if (strlen($part2) > 63) {
                                return false;
                            }
                        } while (1);

                        $part2 .= self::PUNYCODE_DIGIT_MAP[$q];
                        if (strlen($part2) > 63) {
                            return false;
                        }

                        // Adapt bias.
                        $numhandled++;
                        $bias = self::InternalPunycodeAdapt(
                            $delta,
                            $numhandled,
                            $first
                        );
                        $delta = 0;
                        $first = false;
                    }
                }

                $delta++;
                $n++;
            }

            $parts[$num] = $part2;
        }

        return implode(".", $parts);
    }

    public static function ConvertFromPunycode($domain)
    {
        // Reject invalid domain name lengths.
        if (strlen($domain) > 255) {
            return false;
        }

        $parts = explode(".", $domain);

        foreach ($parts as $num => $part) {
            // Reject invalid label lengths.
            $y = strlen($part);
            if ($y > 63) {
                return false;
            }

            // Skip unencoded portions.
            if (substr($part, 0, 4) !== "xn--") {
                continue;
            }

            $part = substr($part, 4);

            // Convert UTF-8 to UTF-32 code points.
            $data = self::Convert($part, self::UTF8, self::UTF32_ARRAY);

            // Handle ASCII code points.
            $hyphen = ord("-");
            for ($x = count($data); $x && $data[$x - 1] !== $hyphen; $x--);
            if (!$x) {
                $data2 = [];
            } else {
                $data2 = array_splice($data, 0, $x - 1);

                array_shift($data);
            }

            $numhandled = count($data2);

            $bias = self::PUNYCODE_INITIAL_BIAS;
            $n = self::PUNYCODE_INITIAL_N;
            $delta = 0;
            $first = true;

            $pos = 0;
            $y = count($data);
            while ($pos < $y) {
                // Calculate and decode a delta from the variable length integer.
                $olddelta = $delta;
                $w = 1;
                $x = 0;
                do {
                    $x += self::PUNYCODE_BASE;

                    $cp = $data[$pos];
                    $pos++;

                    if ($cp >= ord("a") && $cp <= ord("z")) {
                        $digit = $cp - ord("a");
                    } elseif ($cp >= ord("A") && $cp <= ord("Z")) {
                        $digit = $cp - ord("A");
                    } elseif ($cp >= ord("0") && $cp <= ord("9")) {
                        $digit = $cp - ord("0") + 26;
                    } else {
                        return false;
                    }

                    $delta += $digit * $w;
                    if ($delta < 0) {
                        return false;
                    }

                    if ($x <= $bias) {
                        $t = self::PUNYCODE_TMIN;
                    } elseif ($x >= $bias + self::PUNYCODE_TMAX) {
                        $t = self::PUNYCODE_TMAX;
                    } else {
                        $t = $x - $bias;
                    }

                    if ($digit < $t) {
                        break;
                    }

                    $w *= self::PUNYCODE_BASE - $t;
                    if ($w < 0) {
                        return false;
                    }
                } while (1);

                // Adapt bias.
                $numhandled++;
                $bias = self::InternalPunycodeAdapt(
                    $delta - $olddelta,
                    $numhandled,
                    $first
                );
                $first = false;

                // Delta was supposed to wrap around from $numhandled to 0, incrementing $n each time, so fix that now.
                $n += (int) ($delta / $numhandled);
                $delta %= $numhandled;

                // Insert $n (the code point) at the delta position.
                array_splice($data2, $delta, 0, [$n]);
                $delta++;
            }

            $parts[$num] = self::Convert($data2, self::UTF32_ARRAY, self::UTF8);
        }

        return implode(".", $parts);
    }

    // RFC3492 adapt() function.
    protected static function InternalPunycodeAdapt($delta, $numpoints, $first)
    {
        $delta = $first ? (int) ($delta / self::PUNYCODE_DAMP) : $delta >> 1;
        $delta += (int) ($delta / $numpoints);

        $y = self::PUNYCODE_BASE - self::PUNYCODE_TMIN;

        $condval = (int) (($y * self::PUNYCODE_TMAX) / 2);
        for ($x = 0; $delta > $condval; $x += self::PUNYCODE_BASE) {
            $delta = (int) ($delta / $y);
        }

        return (int) ($x +
            (($y + 1) * $delta) / ($delta + self::PUNYCODE_SKEW));
    }
}
?>
