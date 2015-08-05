<?php
/**
  * WARNING: Do not edit by hand, this file was generated by Crank:
  *
  * https://github.com/gocardless/crank
  */

namespace GoCardlessPro\Resources;

/**
  * Look up the name and reachability of a bank.
  */
class BankDetailsLookup extends Base
{



  /**
    * Array of [schemes](#mandates_scheme) supported for this bank account. This
    * will be an empty array if the bank account is not reachable by any
    * schemes.
    *
    * @return Wrapper\NestedArray
    */
    public function available_debit_schemes()
    {
        $field = 'available_debit_schemes';
        if (!property_exists($this->data, $field)) {
            return null;
        }
        return new Wrapper\NestedArray($field, $this->data->{$field});

    }

  /**
    * The name of the bank with which the account is held (if available).
    *
    * @return string
    */
    public function bank_name()
    {
        $field = 'bank_name';
        if (!property_exists($this->data, $field)) {
            return null;
        }
        return $this->data->{$field};
    }

  /**
    * ISO 9362 SWIFT BIC of the bank with which the account is held.
    *
    * @return string
    */
    public function bic()
    {
        $field = 'bic';
        if (!property_exists($this->data, $field)) {
            return null;
        }
        return $this->data->{$field};
    }


  /**
    * Returns a string representation of the project.
    *
    * @return string 
    */
    public function __toString()
    {
        $ret = 'BankDetailsLookup Class (';
        $ret .= print_r($this->data, true);
        return $ret;
    }
}
