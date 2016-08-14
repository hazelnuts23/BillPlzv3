<?php

namespace hazelnuts23\BillPlzv3;

class Billplzv3
{
    private $host = config('billplz.'.env('APP_ENV', 'https://www.billplz.com/api/v3/'));
    private $data = array();
    private $api_key = '';
    private $ch;
    private $sep = '/';
    private $bills = 'bills';
    private $collections = 'collections';
    private $open_collections = 'open_collections';

    public function __construct($data = array())
    {
        if (is_array($data) && (count($data) > 0)) {
            if (isset($data['api_key'])) {
                $this->api_key = $data['api_key'];
            }
            if (isset($data['host'])) {
                $this->host = $data['host'];
            }
        }
    }

    public function create_collection()
    {
        $this->ch = curl_init($this->host . $this->sep . $this->collections);
        if (isset($this->data['logo'])) {
            if (file_exists($this->data['logo'])) {
                $this->data['logo'] = '@' . $this->data['logo'];
            } else {
                $this->error = "logo file not found";
                return false;
            }
        }

        return $this->_run();
    }

    public function open_collections()
    {
        $this->ch = curl_init($this->host . $this->sep . $this->open_collections);
        if (isset($this->data['photo'])) {
            if (file_exists($this->data['photo'])) {
                $this->data['photo'] = '@' . $this->data['photo'];
            } else {
                $this->error = "Photo file not found";
                return false;
            }
        }
    }

    public function create_bill()
    {
        $this->ch = curl_init($this->host . $this->sep . $this->bills);
        return $this->_run();
    }

    public function get_bill($bill_id)
    {
        $this->ch = curl_init($this->host . $this->sep . $this->bills . $this->sep . $bill_id);
        return $this->_run();
    }

    public function delete_bill($bill_id)
    {
        $this->ch = curl_init($this->host . $this->sep . $this->bills . $this->sep . $bill_id);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->_run();
    }


    public  function set_data($data, $data2 = null)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->data[$key] = $value;
            }
        } else if ($data2 !== null) {
            $this->data[$data] = $data2;
        }
    }

    function _run()
    {
        try {

            if ($this->api_key == '') {
                $this->error = 'API key was not set';
                return false;
            }
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->api_key .":");
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 170);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);

            if (count($this->data) > 0) {
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->data);
            }
            $r = curl_exec($this->ch);
            curl_close($this->ch);

            return $r;
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }
    }
}