<?php

namespace app\helps;

use Elliptic\EC;
use EthTool\KeyStore;
use kornrunner\Keccak;
use EthTool\Credential;
use Web3\Web3;
use EthTool\Callback;
use Web3\Utils;
use Web3\Contract;

define('APP_EXTEND', __DIR__ . '/../extend/');

class Eth
{
    // const HOST = 'http://134.175.119.64:8312'; //公司
    protected $host = 'http://47.93.53.153:8555';
    protected $token;

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function wallet()
    {
        $wfn = Credential::newWallet($this->token, APP_EXTEND . 'ethtool/keystore');
        $credential = Credential::fromWallet($this->token, $wfn);
        return $data = [
            'private' => $credential->getPrivateKey(),
            'public' => $credential->getPublicKey(),
            'address' => $credential->getAddress()
        ];
    }

    public function exchange($private = null)
    {
        if ($private == null) {
            return false;
        }
        $ec = new EC('secp256k1');
        $keyPair = $ec->keyFromPrivate($private);
        KeyStore::save($private, $this->token, APP_EXTEND . 'ethtool/keystore');

        return [
            'private' => $keyPair->getPrivate()->toString(16, 2),
            'public' => $keyPair->getPublic()->encode('hex'),
            'address' => '0x' . substr(Keccak::hash(substr(hex2bin($keyPair->getPublic()->encode('hex')), 1), 256), 24)
        ];
    }

    public function balance($address, $block = 'latest', $proportion = 16)
    {
        $web3 = new Web3($this->host);
        $web3->eth->getBalance($address, $block, $callback = new Callback);

        return $callback->result->toString() / pow(10, $proportion);
    }

    public function tokenBalance($address, $tokenAds, $proportion = 4)
    {
        $web3 = new Web3($this->host);
        $contract = $this->loadContract($web3, 'EzToken.abi', $tokenAds);
        $contract->call('balanceOf', $address, $opts = [], $callback = new Callback);

        return $callback->result['balance']->toString() / pow(10, $proportion);
    }

    private function loadContract($web3, $name, $contractAds)
    {
        $file = APP_EXTEND . 'ethtool/contract/build/' . $name;
        $contract = new Contract($web3->provider, file_get_contents($file));
        $contract->at($contractAds);
        return $contract;
    }

    public function transfer($from, $private, $to, $value, $gasPrice = '20', $gasLimit = '0x76c0', $chainId = 1)
    {
        $web3 = new Web3($this->host);
        $callback = new Callback;
        try {
            $keyStore = APP_EXTEND . 'ethtool/keystore/' . substr($from, 2) . '.json';
            $credential = Credential::fromWallet($this->token, $keyStore);
            $address = $credential->getAddress();
            if ($private != $credential->getPrivateKey()) {
                return ['status' => 0, 'msg' => 'private error'];
            }
            $web3->eth->getTransactionCount($address, 'latest', $callback);
            $data = [
                'nonce' => Utils::toHex($callback->result, true),
                'gasPrice' => '0x' . Utils::toWei('20', 'gwei')->toHex(),
                'gasLimit' => $gasLimit,
                'to' => $to,
                'value' => '0x' . Utils::toWei($value, 'ether')->toHex(),
                'data' => '0x' . bin2hex('transfer'),
                'chainId' => $chainId ?: 1
            ];
            $signed = $credential->signTransaction($data); // 进行离线签名
            $web3->eth->sendRawTransaction($signed, $callback);  // 发送裸交易
            return ['status' => 1, 'msg' => 'Success'];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function transferToken($from, $private, $contractAds, $to, $value, $gasPrice = '20', $gasLimit = '0x76c0', $chainId = 1)
    {
        $web3 = new Web3($this->host);
        $cb = new Callback;
        try {
            $keyStore = APP_EXTEND . 'ethtool/keystore/' . substr($from, 2) . '.json';
            $credential = Credential::fromWallet($this->token, $keyStore);
            $address = $credential->getAddress();
            if ($private != $credential->getPrivateKey()) {
                return ['status' => 0, 'msg' => 'private error'];
            }
            $web3->eth->getTransactionCount($address, 'latest', $cb);
            $bet = 10000; // 代币发布时小数点位数
            $value = base_convert($value * $bet, 10, 16);
            $raw = [
                'nonce' => Utils::toHex($cb->result, true),
                'gasPrice' => '0x' . Utils::toWei('20', 'gwei')->toHex(),
                'gasLimit' => '0xea60', //16进制
                'to' => $contractAds, //代币地址
                'value' => '0x0',
                'data' => '0x' . 'a9059cbb' . str_pad(substr($to, 2), 64, "0", STR_PAD_LEFT) . str_pad($value, 64, "0", STR_PAD_LEFT),
                'chainId' => $chainId ?: 1
            ];
            $signed = $credential->signTransaction($raw); // 进行离线签名
            $web3->eth->sendRawTransaction($signed, $cb);  // 发送裸交易

            return ['status' => 1, 'hash' => $signed, 'msg' => 'Success'];
        } catch (\Exception $e) {
            return ['status' => 0, 'msg' => $e->getMessage()];
        }
    }
}