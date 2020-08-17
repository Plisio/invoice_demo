<?php


class PlisioOrderRepository extends Db
{
    private $table = 'plisio_order';

    public function __construct($dsn, $username, $password, $options = [])
    {
        parent::__construct($dsn, $username, $password, $options);
    }


    private function prepareOrderData($data, $fields)
    {
        return array_filter($data, function ($i) use ($fields) {
            return in_array($i, $fields);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function validateRequiredData($data, $extra = [])
    {
        $required = array_merge(['order_id', 'plisio_invoice_id'], $extra);
        $invalid = [];
        foreach ($required as $item) {
            if (!isset($data[$item]) || empty($data[$item])) {
                $invalid[] = $item;
            }
        }
        return $invalid;
    }

    public function get($invoice_id){
        $query = 'SELECT * FROM ' . $this->table . ' WHERE plisio_invoice_id=:plisio_invoice_id';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':plisio_invoice_id', $invoice_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function add($data, $whiteLabel = false)
    {
        $invalid = $this->validateRequiredData($data);
        if (count($invalid) === 0) {
            $fields = [
                'order_id', 'plisio_invoice_id'
            ];
            if ($whiteLabel){
                $fields = array_merge($fields, ['amount', 'pending_amount', 'wallet_hash', 'psys_cid',
                    'currency', 'status', 'source_currency', 'source_rate', 'expire_utc',
                    'confirmations', 'expected_confirmations', 'qr_code', 'tx_urls']);
            }
            $data['expire_utc'] = (new DateTime())->setTimestamp($data['expire_utc'])->format('Y-m-d H:i:s');
            $keys = array_map(function ($i) {
                return ':' . $i;
            }, $fields);

            $query = 'INSERT INTO `' . $this->table . '` (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $keys) . ')';
            $stmt = $this->pdo->prepare($query);
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $stmt->bindValue(':' . $field, $data[$field]);
                } else {
                    $stmt->bindValue(':' . $field, null);
                }
            }
            return $stmt->execute();
        }
        return false;
    }

    public function updateOrder($data)
    {
        $invalid = $this->validateRequiredData($data);
        if (count($invalid) === 0 && isset($data['wallet_hash']) && !empty($data['wallet_hash'])) {
            $fields = ['pending_amount', 'status', 'qr_code', 'confirmations', 'tx_urls'];
            $where = [
                'order_id' => $data['order_id'],
                'plisio_invoice_id' => $data['plisio_invoice_id']
            ];
            $keys = array_map(function ($i) {
                return $i . '= :' . $i;
            }, $fields);
            $whereKeys = array_map(function ($i) {
                return $i . '= :' . $i;
            }, array_keys($where));

            $query = 'UPDATE `' . $this->table . '` SET ' . implode(', ', $keys) . ' WHERE ' . implode(' AND ', $whereKeys);
            $stmt = $this->pdo->prepare($query);
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $stmt->bindValue(':' . $field, $data[$field]);
                } else {
                    $stmt->bindValue(':' . $field, null);
                }
            }
            foreach (array_keys($where) as $field) {
                if (isset($where[$field])) {
                    $stmt->bindValue(':' . $field, $where[$field]);
                }
            }
            $res = $stmt->execute();
            return  $res;
        }
        return false;
    }
}