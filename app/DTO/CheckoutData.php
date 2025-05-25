<?php
/**
 * GB Developer
 *
 * @category GB_Developer
 * @package  GB
 *
 * @copyright Copyright (c) 2025 GB Developer.
 *
 * @author Geovan Brambilla <geovangb@gmail.com>
 */

namespace App\DTO;

class CheckoutData
{
    public string $name;
    public string $email;
    public string $phone;
    public string $cep;
    public string $address;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->cep = $data['cep'];
        $this->address = $data['address'];
    }
}
