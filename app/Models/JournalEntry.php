<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_POSTED = 'posted';
    public const STATUS_REVERSED = 'reversed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'journal_number', 'entry_date', 'description', 'reference_type', 'reference_id',
        'status', 'posted_at', 'posted_by', 'reversed_by_entry_id', 'created_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posted_at'  => 'datetime',
    ];

    /* ── Relationships ── */

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function postedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reversalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'reversed_by_entry_id');
    }

    /* ── Aggregates ── */

    public function getTotalDebit(): float
    {
        return (float) $this->lines->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return (float) $this->lines->sum('credit');
    }

    /* ── Status Checks ── */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPosted(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isReversed(): bool
    {
        return $this->status === self::STATUS_REVERSED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isReversible(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    /**
     * Statuses whose lines should affect the general ledger.
     *
     * A reversed original remains ledger-affecting so the original entry and
     * its posted reversal net to zero in reports.
     */
    public static function ledgerStatuses(): array
    {
        return [self::STATUS_POSTED, self::STATUS_REVERSED];
    }

    /* ── Status Labels ── */

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'badge status-draft',
            self::STATUS_POSTED    => 'badge status-completed',
            self::STATUS_REVERSED  => 'badge status-cancelled',
            self::STATUS_CANCELLED => 'badge status-cancelled',
            default     => 'badge badge-gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT     => 'Draft',
            self::STATUS_POSTED    => 'Posted',
            self::STATUS_REVERSED  => 'Reversed',
            self::STATUS_CANCELLED => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }
}
