<?php namespace App\Services;
use Illuminate\Support\Facades\Hash;use Illuminate\Support\Facades\Http;
class LicenseVerifier{private const A0="\x68\x74\x74\x70\x73\x3a\x2f\x2f\x64\x65\x6d\x6f\x2e\x72\x65\x70\x72\x6f\x70\x65\x72\x74\x79\x63\x6d\x73\x2e\x63\x6f\x6d\x2f\x61\x70\x69\x2f\x6c\x69\x63\x65\x6e\x73\x65\x2f\x76\x65\x72\x69\x66\x79";
public static function call(string $k,string $d,string $i):array{return Http::timeout(12)->acceptJson()->post(self::A0,['key'=>$k,'domain'=>$d,'ip'=>$i])->json()??[];}
public static function verify(array $b):bool{$s=chr(0x72).chr(0x65).chr(0x70).chr(0x72).chr(0x6f).chr(0x70).chr(0x65).chr(0x72).chr(0x74).chr(0x79).chr(0x63).chr(0x6d).chr(0x73);return($b["\x76\x61\x6c\x69\x64"]??false)===true&&isset($b["\x74\x6f\x6b\x65\x6e"])&&Hash::check($s,$b["\x74\x6f\x6b\x65\x6e"]);}}
