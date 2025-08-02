<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InsufficientFundsException;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];
    
    protected $casts = [
        'balance' => 'decimal:2'
    ];

    /**
     * Отношение к пользователю
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Транзакции кошелька
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Пополнение баланса
     */
    public function deposit(float $amount, array $meta = [], string $description = null) 
    {
        if ($amount <= 0) {
            return false;
        }

        $transaction = $this->createTransaction(abs($amount), 'deposit', $meta, $description);
        $this->increment('balance', $amount);
        
        return $transaction;
    }

    /**
     * Списание средств
     */
    public function withdraw(float $amount, array $meta = [], string $description = null)
    {
        if ($amount <= 0) {
            return false;
        }

        if ($this->balance < $amount) {
            return false;
        }

        $transaction = $this->createTransaction(abs($amount), 'withdrawal', $meta, $description);
        $this->decrement('balance', $amount);
        
        return $transaction;
    }

    /**
     * Проверка достаточности средств
     */
    public function hasSufficientFunds(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Создание транзакции (приватный метод)
     */
    protected function createTransaction(float $amount, string $type, array $meta = [], string $description = null): Transaction
    {
        return $this->transactions()->create([
            'amount' => $amount,
            'type' => $type,
            'status' => 'completed',
            'description' => $description,
            'meta' => $meta ? json_encode($meta) : null,
        ]);
    }
}