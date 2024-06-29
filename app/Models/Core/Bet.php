<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Core;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Bet
 *
 * @property int|null $id
 * @property int $addedToCoupon
 * @property string $score
 * @property int $status
 * @property string $populerBetId
 * @property string $sportId
 * @property string $eventId
 * @property string $eventName
 * @property string $eventDate
 * @property string $marketNo
 * @property string $marketVersion
 * @property string $outcomeNo
 * @property string $outcomeName
 * @property string $odd
 * @property string $showOdd
 * @property string $minimumBetCount
 * @property string $competitionId
 * @property string $competitionAcronym
 * @property string $competitionName
 * @property string $countryId
 * @property string $betradarId
 * @property string $isKingBet
 * @property string $isKingLive
 * @property string $isKingOdd
 * @property string $isKingMbs
 * @property string $isLive
 * @property string $playedRatio
 * @property string $isDeleted
 * @property string $marketType
 * @property string $marketSubType
 * @property string $marketId
 * @property string $marketName
 * @property string $webOdd
 * @property string $totalPlayed
 * @property string $totalPlayedRoundStr
 * @property string $longEventDate
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @package App\Models\Core
 * @method static \Illuminate\Database\Eloquent\Builder|Bet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereAddedToCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereBetradarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereCompetitionAcronym($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereCompetitionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereCompetitionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereIsKingBet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereIsKingLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereIsKingMbs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereIsKingOdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereIsLive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereLongEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMarketName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMarketNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMarketSubType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMarketType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMarketVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMinimumBetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereOdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereOutcomeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereOutcomeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet wherePlayedRatio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet wherePopulerBetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereShowOdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereTotalPlayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereTotalPlayedRoundStr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereWebOdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet withoutTrashed()
 * @mixin \Eloquent
 */
class Bet extends Model
{
	use SoftDeletes;
	protected $table = 'bet';
	public static $snakeAttributes = false;

	protected $casts = [
		'addedToCoupon' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'addedToCoupon',
		'score',
		'status',
		'populerBetId',
		'sportId',
		'eventId',
		'eventName',
		'eventDate',
		'marketNo',
		'marketVersion',
		'outcomeNo',
		'outcomeName',
		'odd',
		'showOdd',
		'minimumBetCount',
		'competitionId',
		'competitionAcronym',
		'competitionName',
		'countryId',
		'betradarId',
		'isKingBet',
		'isKingLive',
		'isKingOdd',
		'isKingMbs',
		'isLive',
		'playedRatio',
		'isDeleted',
		'marketType',
		'marketSubType',
		'marketId',
		'marketName',
		'webOdd',
		'totalPlayed',
		'totalPlayedRoundStr',
		'longEventDate'
	];
}
