<?php  declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const UNASSIGNED = 'unassigned';
    const CANCELLED = 'cancelled';
    const TAKEN = 'taken';
    const ORDER_ALREADY_BEEN_TAKEN = 'order_already_been_taken';

    protected $fillable = ['id', 'distance', 'status'];
}