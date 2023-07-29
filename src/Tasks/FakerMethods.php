<?php

namespace Shift\Cli\Tasks;

use Shift\Cli\Contracts\Task;
use Shift\Cli\Facades\Comment;
use Shift\Cli\Traits\FindsFiles;

class FakerMethods implements Task
{
    use FindsFiles;

    public function perform(): int
    {
        $map = array_combine(array_map('strtolower', $this->properties()), $this->properties());
        $pattern = '/(\Wfaker->(?:(?:unique|optional)\(\)->)?)(' . implode('|', $map) . ')(\W)/i';

        foreach ($this->findFiles() as $file) {
            $contents = file_get_contents($file);

            if (! str_contains($contents, 'faker->')) {
                continue;
            }

            $dirty = false;
            $contents = preg_replace('/faker->(unique|optional)->/i', 'faker->$1()->', $contents, count: $dirty);
            $contents = preg_replace_callback(
                $pattern,
                function ($matches) use (&$dirty, $map) {
                    if ($matches[3] === '(') {
                        return $matches[0];
                    }

                    $dirty = true;

                    return $matches[1] . $map[strtolower($matches[2])] . '()' . $matches[3];
                },
                $contents
            );

            if (! $dirty) {
                continue;
            }

            file_put_contents($file, $contents);
        }

        $this->remainingModifiers();

        return 0;
    }

    private function remainingModifiers(): void
    {
        $paths = $this->findFilesContaining('/faker->(unique|optional|valid)\([^)]/');
        if (empty($paths)) {
            return;
        }

        $comment = 'Shift detected uses of Faker modifiers which received arguments. Shift was not able to reliably convert these instances. You should review the following files and convert any remaining property access chained after a modifier.';
        Comment::addComment($comment, $paths, 'https://fakerphp.github.io/#modifiers');
    }

    private function properties(): array
    {
        return [
            'address',
            'amPm',
            'asciify',
            'biasedNumberBetween',
            'boolean',
            'bothify',
            'bs',
            'buildingNumber',
            'catchPhrase',
            'century',
            'chrome',
            'city',
            'cityPrefix',
            'citySuffix',
            'colorName',
            'company',
            'companyEmail',
            'companySuffix',
            'country',
            'countryCode',
            'countryISOAlpha3',
            'creditCardDetails',
            'creditCardExpirationDate',
            'creditCardExpirationDateString',
            'creditCardNumber',
            'creditCardType',
            'currencyCode',
            'date',
            'dateTime',
            'dateTimeAD',
            'dateTimeBetween',
            'dateTimeInInterval',
            'dateTimeThisCentury',
            'dateTimeThisDecade',
            'dateTimeThisMonth',
            'dateTimeThisYear',
            'dayOfMonth',
            'dayOfWeek',
            'domainName',
            'domainWord',
            'e164PhoneNumber',
            'ean13',
            'ean8',
            'email',
            'emoji',
            'file',
            'fileExtension',
            'firefox',
            'firstName',
            'firstNameFemale',
            'firstNameMale',
            'freeEmail',
            'freeEmailDomain',
            'getDefaultTimezone',
            'hexColor',
            'hslColor',
            'hslColorAsArray',
            'iban',
            'image',
            'imageUrl',
            'imei',
            'internetExplorer',
            'ipv4',
            'ipv6',
            'isbn10',
            'isbn13',
            'iso8601',
            'jobTitle',
            'languageCode',
            'lastName',
            'latitude',
            'lexify',
            'linuxPlatformToken',
            'linuxProcessor',
            'localCoordinates',
            'localIpv4',
            'locale',
            'longitude',
            'macAddress',
            'macPlatformToken',
            'macProcessor',
            'md5',
            'mimeType',
            'month',
            'monthName',
            'name',
            'numberBetween',
            'numerify',
            'opera',
            'paragraph',
            'paragraphs',
            'passthrough',
            'password',
            'phoneNumber',
            'postcode',
            'randomAscii',
            'randomDigit',
            'randomDigitNot',
            'randomDigitNotNull',
            'randomElement',
            'randomElements',
            'randomFloat',
            'randomHtml',
            'randomKey',
            'randomLetter',
            'randomNumber',
            'realText',
            'realTextBetween',
            'regexify',
            'rgbColor',
            'rgbColorAsArray',
            'rgbCssColor',
            'rgbaCssColor',
            'rgbcolor',
            'safari',
            'safeColorName',
            'safeEmail',
            'safeEmailDomain',
            'safeHexColor',
            'secondaryAddress',
            'sentence',
            'sentences',
            'setDefaultTimezone',
            'sha1',
            'sha256',
            'shuffle',
            'shuffleArray',
            'shuffleString',
            'slug',
            'state',
            'stateAbbr',
            'streetAddress',
            'streetName',
            'streetSuffix',
            'suffix',
            'swiftBicNumber',
            'text',
            'time',
            'timezone',
            'title',
            'titleFemale',
            'titleMale',
            'tld',
            'toLower',
            'toUpper',
            'tollFreePhoneNumber',
            'unixTime',
            'url',
            'userAgent',
            'userName',
            'uuid',
            'windowsPlatformToken',
            'word',
            'words',
            'year',
        ];
    }
}
