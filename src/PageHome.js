import React, {useState, useEffect} from 'react';
import {useSearchParams} from 'react-router-dom';
import {Model}                      from './Model';
import moment                       from 'moment/moment';
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome';
import * as Fa           from '@fortawesome/free-solid-svg-icons';
// ->
const PRIMARY_COLOR      = process.env.REACT_APP_COLOR_PRIMARY;
const SUCCESS_COLOR      = process.env.REACT_APP_COLOR_SUCCESS;
const DANGER_COLOR       = process.env.REACT_APP_COLOR_DANGER;
const WARNING_COLOR      = process.env.REACT_APP_COLOR_WARNING;
const LIGHT_COLOR        = process.env.REACT_APP_COLOR_LIGHT;
const DARK_COLOR         = process.env.REACT_APP_COLOR_DARK;
// ->
const API_URL_CHECK      = process.env.REACT_APP_API_URL_CHECK;
const API_URL_MATCH_GET  = process.env.REACT_APP_API_URL_MATCH_GET;
const MATCHES_PER_COUPON = parseInt(process.env.REACT_APP_MATCHES_PER_COUPON);
// ->
const couponModel        = new Model('coupon');
const matchModel         = new Model('match');
// ->
export const PageHome    = () => {
    const [baseError, setBaseError]             = useState(null);
    const [baseLoading, setBaseLoading]         = useState(true);
    const [coupons, setCoupons]                 = useState([]);
    const [activeIndex, setActiveIndex]         = useState(null);
    const [totalEarnings, setTotalEarnings]     = useState(0);
    const [totalSpent, setTotalSpent]           = useState(0);
    const [searchParams]                        = useSearchParams();
    // ->
    const fetchDataFromApi                      = async (url) => {
        try {
            const response = await fetch(url);
            return await response.json();
        }
        catch (error) {
            console.error('Maç verilerini çekerken hata oluştu:', error);
            return null;
        }
    };
    // ->
    const createCoupon_GetAvailableMatches      = async (matches) => {
        const availableMatches = [];
        const matchEvents = new Set();
        for (const match of matches) {
            const matchKey = `${match.eventName}-${match.eventDate}`;
            if (!matchEvents.has(matchKey)) {
                const exists = await createCoupon_CheckMatchExists(match.eventName, match.marketName, match.outcomeName, match.populerBetId);
                if (!exists) {
                    availableMatches.push(match);
                    matchEvents.add(matchKey);
                }
            }
        }
        return availableMatches;
    };
    const createCoupon_CheckMatchExists         = (eventName, marketName, outcomeName, populerBetId) => {
        return new Promise((resolve, reject) => {
            matchModel.GetAll({
                                  // -> Edit : !!!
                                  queryParams    : [
                                      {field: 'populerBetId', operator: '==', value: populerBetId},
                                      {field: 'marketName', operator: '==', value: marketName}
                                  ],
                                  callBackSuccess: response => {
                                      resolve(response.length > 0);
                                  },
                                  callBackError  : error => {
                                      reject(error);
                                  }
                              });
        });
    };
    const createCoupon_GenerateCoupon           = (matches) => {
        const coupons = [];
        while (matches.length >= MATCHES_PER_COUPON) {
            const coupon = matches.splice(0, MATCHES_PER_COUPON);
            coupons.push(coupon);
        }
        return coupons;
    };
    const createCoupon_GenerateCouponId         = () => {
        return 'coupon_' + Math.random().toString(36).substr(2, 9);
    };
    const createCoupon_SaveCouponToDb           = (coupon, couponId) => {
        const eventIds = coupon.map(match => match.eventId);
        couponModel.Create({
                               data           : {couponId, eventIds, status: 'Pending', created_at: moment().format()},
                               callBackSuccess: () => {
                                   coupon.forEach(match => {
                                       matchModel.Create({
                                                             data           : {...match, couponId, status: 'Pending', score: '-/-'},
                                                             callBackSuccess: () => {
                                                                 console.log('Maç başarıyla oluşturuldu:', match.eventId, 'Kupon ID:', couponId);
                                                             },
                                                             callBackError  : error => {
                                                                 console.error('Maç oluştururken hata oluştu:', error);
                                                             }
                                                         });
                                   });
                               },
                               callBackError  : error => {
                                   console.error('Kupon oluştururken hata oluştu:', error);
                               }
                           });
    };
    const createCoupon                          = async () => {
        const matchesData = await fetchDataFromApi(API_URL_MATCH_GET);
        if (matchesData) {
            // -> Edit : Kg Var & Yok, 2.5 Alt & Üst
            const matchResultMatches      = matchesData.data.filter(data => data.marketName === 'Maç Sonucu');
            const bothTeamsToScoreMatches = matchesData.data.filter(data => data.marketName === 'Karşılıklı Gol');
            const overUnderMatches        = matchesData.data.filter(data => data.marketName === 'Altı/Üstü 2,5');
            const allMatches              = [...matchResultMatches, ...bothTeamsToScoreMatches, ...overUnderMatches];
            if (allMatches.length < MATCHES_PER_COUPON) {
                console.error('Yeterli maç verisi yok.');
                return;
            }
            try {
                const availableMatches = await createCoupon_GetAvailableMatches(allMatches);
                const coupons = createCoupon_GenerateCoupon(availableMatches);
                coupons.forEach(coupon => {
                    const couponId = createCoupon_GenerateCouponId();
                    createCoupon_SaveCouponToDb(coupon, couponId);
                });
            }
            catch (error) {
                console.error('Veritabanındaki maçları kontrol ederken hata oluştu:', error);
            }
        }
        else {
            console.error('API\'den veri çekerken hata oluştu.');
        }
    };
    // ->
    const checkMatchAndCoupon_UpdateMatchStatus = (dbMatch, apiMatch) => {
        const homeScore = apiMatch.sc.ht.r ?? '-';
        const awayScore = apiMatch.sc.at.r ?? '-';
        const score     = `${homeScore}/${awayScore}`;
        let status      = 'Pending';
        if (homeScore !== '-' && awayScore !== '-') {
            // -> Edit : Kg Var & Yok, 2.5 Alt & Üst
            if ((dbMatch.outcomeName === '1' && homeScore > awayScore) ||
                (dbMatch.outcomeName === '0' && homeScore === awayScore) ||
                (dbMatch.outcomeName === '2' && homeScore < awayScore) ||
                (dbMatch.outcomeName === 'Var' && homeScore > 0 && awayScore > 0) ||
                (dbMatch.outcomeName === 'Yok' && (homeScore === 0 || awayScore === 0)) ||
                (dbMatch.outcomeName === 'Alt' && (homeScore + awayScore <= 2.5)) ||
                (dbMatch.outcomeName === 'Üst' && (homeScore + awayScore > 2.5))) {
                status = 'Win';
            }
            else {
                status = 'Lost';
            }
        }
        matchModel.Update({
                              id  : dbMatch.id,
                              data: {status, score}
                          });
    };
    const checkMatchAndCoupon                   = async () => {
        const todayMatches     = await fetchDataFromApi(`${API_URL_CHECK}${moment().format('YYYY-MM-DD')}`);
        const yesterdayMatches = await fetchDataFromApi(`${API_URL_CHECK}${moment().subtract(1, 'days').format('YYYY-MM-DD')}`);
        if (todayMatches) {
            const matchMap = new Map(todayMatches.data.matches.map(match => [match.sgId, match]));
            matchModel.GetAll({
                                  callBackSuccess: response => {
                                      response.forEach(dbMatch => {
                                          const apiMatch = matchMap.get(dbMatch.eventId);
                                          if (apiMatch) {
                                              checkMatchAndCoupon_UpdateMatchStatus(dbMatch, apiMatch);
                                          }
                                      });
                                  },
                                  callBackError  : error => {
                                      console.error('Bekleyen maçları çekerken hata oluştu:', error);
                                  }
                              });
        }
        if (yesterdayMatches) {
            const matchMap = new Map(yesterdayMatches.data.matches.map(match => [match.sgId, match]));
            matchModel.GetAll({
                                  queryParams    : [{field: 'status', operator: '==', value: 'Pending'}],
                                  callBackSuccess: response => {
                                      response.forEach(dbMatch => {
                                          const apiMatch = matchMap.get(dbMatch.eventId);
                                          if (apiMatch) {
                                              checkMatchAndCoupon_UpdateMatchStatus(dbMatch, apiMatch);
                                          }
                                      });
                                  },
                                  callBackError  : error => {
                                      console.error('Bekleyen maçları çekerken hata oluştu:', error);
                                  }
                              });
        }
    };
    // ->
    const displayData                           = () => {
        couponModel.GetAll({
                               orderParams    : [{field: 'created_at', direction: 'desc'}],
                               callBackSuccess: async (couponData) => {
                                   const enrichedCoupons = await Promise.all(
                                       couponData.map(async (coupon) => {
                                           return new Promise((resolve, reject) => {
                                               matchModel.GetAll({
                                                                     queryParams    : [{field: 'couponId', operator: '==', value: coupon.couponId}],
                                                                     callBackSuccess: (matches) => {
                                                                         const totalOdds     = matches.reduce((acc, match) => acc * parseFloat(match.odd), 1).toFixed(2);
                                                                         const allMatchesWon = matches.every(match => match.status === 'Win');
                                                                         resolve({...coupon, matches, totalOdds, allMatchesWon});
                                                                     },
                                                                     callBackError  : (error) => {
                                                                         console.error('Maç verilerini çekerken hata oluştu:', error);
                                                                         reject(error);
                                                                     }
                                                                 });
                                           });
                                       })
                                   );
                                   // ->
                                   enrichedCoupons.forEach(coupon => {
                                       let couponStatus = 'Pending';
                                       const now        = moment();
                                       let hasLostMatch = false;
                                       coupon.matches.forEach(match => {
                                           const matchEndTime = moment(match.eventDate).add(3, 'hours');
                                           if (now.isAfter(matchEndTime)) {
                                               if (match.status === 'Lost') {
                                                   hasLostMatch = true;
                                               }
                                           }
                                       });
                                       if (hasLostMatch) {
                                           couponStatus = 'Lost';
                                       }
                                       else if (coupon.matches.every(match => match.status === 'Win')) {
                                           couponStatus = 'Win';
                                       }
                                       coupon.status = couponStatus;
                                   });
                                   const earnings = enrichedCoupons.reduce((acc, coupon) => coupon.allMatchesWon ? acc + (coupon.totalOdds * 1000) : acc, 0);
                                   const spent    = enrichedCoupons.length * 1000;
                                   setCoupons(enrichedCoupons);
                                   setTotalEarnings(earnings);
                                   setTotalSpent(spent);
                                   setBaseLoading(false);
                               },
                               callBackError  : (error) => {
                                   console.error('Kupon verilerini çekerken hata oluştu:', error);
                                   setBaseError('Kupon verilerini çekerken hata oluştu.');
                                   setBaseLoading(false);
                               }
                           });
    };
    const displayData_ToggleCouponDetails       = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
        setCoupons((prevCoupons) =>
                       prevCoupons.map((coupon, i) => ({
                           ...coupon,
                           showDetails: i === index ? !coupon.showDetails : false
                       }))
        );
    };
    // ->
    const helper_FormatDate                     = (dateString) => {
        const options = {year: '2-digit', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'};
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };
    const helper_FormatMoney                    = (moneyString) => {
        return moneyString.toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'}).replace('TRY', '');
    };
    // ->
    useEffect(() => {
        createCoupon();
        checkMatchAndCoupon();
        displayData();
    }, []);
    // ->
    const balance      = totalEarnings - totalSpent;
    const balanceColor = balance >= 0 ? SUCCESS_COLOR : DANGER_COLOR;
    // ->
    if (baseLoading) {
        return (
            <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
                <div style={{width: '100%', maxWidth: '800px'}}>
                    <div style={{
                        padding     : '0 10px',
                        marginBottom   : '10px',
                        backgroundColor: LIGHT_COLOR,
                        border         : `1px solid ${DARK_COLOR}`,
                        display        : 'flex',
                        justifyContent : 'space-between',
                        alignItems     : 'center',
                        borderRadius: '5px',
                        minHeight   : '50px'
                    }}>
                        <div>Xsh Bet Simulator</div>
                        <div>
                            <div style={{
                                textAlign      : 'center',
                                backgroundColor: WARNING_COLOR,
                                color          : 'white',
                                borderRadius   : '5px',
                                padding        : '2px 5px',
                                minWidth       : '120px'
                            }}>
                                YÜKLENİYOR
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
    if (baseError) {
        return (
            <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
                <div style={{width: '100%', maxWidth: '800px'}}>
                    <div style={{
                        padding     : '0 10px',
                        marginBottom   : '10px',
                        backgroundColor: LIGHT_COLOR,
                        border         : `1px solid ${DARK_COLOR}`,
                        display        : 'flex',
                        justifyContent : 'space-between',
                        alignItems     : 'center',
                        borderRadius: '5px',
                        minHeight   : '50px'
                    }}>
                        <div>Xsh Bet Simulator</div>
                        <div>
                            <div style={{
                                textAlign      : 'center',
                                backgroundColor: DANGER_COLOR,
                                color          : 'white',
                                borderRadius   : '5px',
                                padding        : '2px 5px',
                                minWidth       : '120px'
                            }}>
                                SİSTEM HATASI
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
    // ->
    return (
        <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
            <div style={{width: '100%', maxWidth: '800px'}}>
                <div style={{
                    padding     : '0 10px',
                    marginBottom   : '10px',
                    backgroundColor: LIGHT_COLOR,
                    border         : `1px solid ${DARK_COLOR}`,
                    display        : 'flex',
                    justifyContent : 'space-between',
                    alignItems     : 'center',
                    borderRadius: '5px',
                    minHeight   : '50px'
                }}>
                    <div>
                        <div style={{marginBottom: '2px'}}>Xsh Bet Simulator</div>
                        <div>Generate Betting Slips</div>
                    </div>
                    <div>
                        <div style={{
                            textAlign      : 'right',
                            backgroundColor: balanceColor,
                            color          : 'white',
                            borderRadius   : '5px',
                            padding        : '2px 10px',
                            minWidth       : '120px',
                            marginBottom   : '2px'
                        }}>
                            {helper_FormatMoney(balance)} TL
                        </div>
                        <div style={{
                            textAlign      : 'right',
                            backgroundColor: WARNING_COLOR,
                            color          : 'white',
                            borderRadius   : '5px',
                            padding        : '2px 10px',
                            minWidth       : '120px'
                        }}>
                            {helper_FormatMoney(totalSpent)} TL
                        </div>
                    </div>
                </div>
                <div>
                    <div><img src={require('./image-header.webp')} style={{
                        width       : '100%',
                        height      : '200px',
                        objectFit   : 'cover',
                        marginBottom: '10px',
                        borderRadius: '5px'
                    }} alt="Xsh Bet Simulator"/></div>
                </div>
                {coupons.map((coupon, index) => (
                    <div key={coupon.couponId} style={{
                        marginBottom: '5px',
                        border      : `1px solid ${DARK_COLOR}`,
                        borderRadius: '5px',
                        transition  : 'margin 0.3s'
                    }}>
                        <div onClick={() => displayData_ToggleCouponDetails(index)} style={{
                            cursor         : 'pointer',
                            padding        : '10px',
                            backgroundColor: LIGHT_COLOR,
                            display        : 'flex',
                            justifyContent : 'space-between',
                            alignItems: 'center',
                            gap       : 10
                        }}>
                            <div style={{marginRight: 'auto'}}>{moment(coupon.created_at).format('DD-MM-YY HH:mm')}</div>
                            {coupon.allMatchesWon && (
                                <div style={{textAlign: 'right', marginRight: '10px'}}>
                                    {helper_FormatMoney(coupon.totalOdds * 1000)} TL
                                </div>
                            )}
                            <div style={{
                                backgroundColor: coupon.status === 'Win' ? SUCCESS_COLOR : (coupon.status === 'Lost' ? DANGER_COLOR : WARNING_COLOR),
                                width    : '60px',
                                textAlign: 'center',
                                color          : 'white',
                                borderRadius   : '5px',
                                padding  : '5px'
                            }}>
                                {coupon.status}
                            </div>
                            <div style={{
                                backgroundColor: PRIMARY_COLOR,
                                width          : '40px',
                                textAlign      : 'center',
                                color          : 'white',
                                borderRadius   : '5px',
                                padding        : '5px'
                            }}>{coupon.totalOdds}</div>
                        </div>
                        {coupon.showDetails && (
                            <div style={{backgroundColor: '#FFFFFF', borderTop: `1px solid ${DARK_COLOR}`}}>
                                {coupon.matches.map((match, matchIndex) => {
                                    // -> Edit : Kg Var & Yok, 2.5 Alt & Üst
                                    switch (match.outcomeName) {
                                        case 'Var':
                                            match.outcomeName = 'Kg Var';
                                            break;
                                        case 'Yok':
                                            match.outcomeName = 'Kg Yok';
                                            break;
                                        case 'Alt':
                                            match.outcomeName = '2.5 Alt';
                                            break;
                                        case 'Üst':
                                            match.outcomeName = '2.5 Üst';
                                            break;
                                        default:
                                    }
                                    const isLastRow = matchIndex === coupon.matches.length - 1;
                                    return (
                                        <div key={match.eventId} style={{
                                            borderBottom  : isLastRow ? 'unset' : `1px solid ${DARK_COLOR}`,
                                            padding       : '10px',
                                            display       : 'flex',
                                            justifyContent: 'space-between',
                                            alignItems: 'center',
                                            gap       : 10
                                        }}>
                                            <div style={{marginRight: 'auto', textOverflow: 'ellipsis', whiteSpace: 'nowrap', overflow: 'hidden', maxWidth: '200px'}}>{match.eventName}</div>
                                            <div style={{textAlign: 'center'}} className="hide-mobile">{helper_FormatDate(match.eventDate)}</div>
                                            <div style={{textAlign: 'center'}}>{match.score}</div>
                                            <div style={{
                                                backgroundColor: match.status === 'Win' ? SUCCESS_COLOR : (match.status === 'Lost' ? DANGER_COLOR : WARNING_COLOR),
                                                width    : '60px',
                                                textAlign: 'center',
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding  : '5px'
                                            }}>{match.outcomeName}</div>
                                            <div style={{
                                                backgroundColor: match.status === 'Win' ? SUCCESS_COLOR : (match.status === 'Lost' ? DANGER_COLOR : WARNING_COLOR),
                                                width          : '40px',
                                                textAlign      : 'center',
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding        : '5px'
                                            }}>{parseFloat(match.odd).toFixed(2)}</div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};
