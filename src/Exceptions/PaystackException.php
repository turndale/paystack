<?php
namespace HelloFromSteve\Paystack\Exceptions;

use Exception;

class PaystackException extends Exception 
{
    protected string $type;
    protected ?string $paystackCode;
    protected array $meta;

    public function __construct(
        string $message, 
        int $httpCode = 0, 
        string $type = 'api_error', 
        ?string $paystackCode = null, 
        array $meta = []
    ) {
        parent::__construct($message, $httpCode);
        $this->type = $type;
        $this->paystackCode = $paystackCode;
        $this->meta = $meta;
    }

    public function getType(): string { return $this->type; }
    public function getPaystackCode(): ?string { return $this->paystackCode; }
    public function getMeta(): array { return $this->meta; }
    public function getNextStep(): ?string { return $this->meta['nextStep'] ?? null; }
}