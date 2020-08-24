<?php

namespace concepture\yii2logic\validators;

use yii\validators\Validator;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Exception;

/**
 * Phone validator class that validates phone numbers for given 
 * country and formats.
 * Country codes and attributes value should be ISO 3166-1 alpha-2 codes
 * @property string $countryAttribute The country code attribute of model
 * @property string $country The country is fixed
 * @property bool $strict If country is not set or selected adds error
 * @property bool $format If phone number is valid formats value with 
 *          libphonenumber/PhoneNumberFormat const (default to INTERNATIONAL)
 */
class PhoneValidator extends Validator
{

    public $strict = true;
    public $countryAttribute;
    public $country;
    public $format = true;

    public function validateAttribute($model, $attribute)
    {
        if ($this->format === true) {
            $this->format = PhoneNumberFormat::INTERNATIONAL;
        }
        // if countryAttribute is set
        if (!isset($country) && isset($this->countryAttribute)) {
            $countryAttribute = $this->countryAttribute;
            $country = $model->$countryAttribute;
        }

        // if country is fixed
        if (!isset($country) && isset($this->country)) {
            $country = $this->country;
        }

        // if none select from our models with best effort
        if (!isset($country) && isset($model->country_code))
            $country = $model->country_code;

        if (!isset($country) && isset($model->country))
            $country = $model->country;


        // if none and strict
        if (!isset($country) && $this->strict) {
            $this->addError($model, $attribute, \Yii::t('core', 'For phone validation country required'));
            return false;
        }

        if (!isset($country)) {
            return true;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($model->$attribute, $country);
            if ($phoneUtil->isValidNumber($numberProto)) {
                if (is_numeric($this->format)) {
                    $model->$attribute = $phoneUtil->format($numberProto, $this->format);
                }
                return true;
            } else {
                $this->addError($model, $attribute, \Yii::t('core', 'Phone number does not seem to be a valid phone number'));
                return false;
            }
        } catch (NumberParseException $e) {
            $this->addError($model, $attribute, \Yii::t('core', 'Unexpected Phone Number Format'));
        } catch (Exception $e) {
            $this->addError($model, $attribute, \Yii::t('core', 'Unexpected Phone Number Format or Country Code'));
        }
    }

    protected function validateValue($value)
    {
        if ($this->format === true) {
            $this->format = PhoneNumberFormat::INTERNATIONAL;
        }

        // if country is fixed
        if (!isset($country) && isset($this->country)) {
            $country = $this->country;
        }


        // if none and strict
        if (!isset($country) && $this->strict) {

            return false;
        }

        if (!isset($country)) {
            return null;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($value, $country);
            if ($phoneUtil->isValidNumber($numberProto)) {
                if (is_numeric($this->format)) {
                    $value = $phoneUtil->format($numberProto, $this->format);
                }
                return null;
            } else {
                return false;
            }
        } catch (NumberParseException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
