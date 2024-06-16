import React, {useState, useEffect} from 'react';
import {useSearchParams} from 'react-router-dom';
import {Model}                      from './Model';
import moment                       from 'moment/moment';
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome';
import * as Fa           from '@fortawesome/free-solid-svg-icons';
const PRIMARY_COLOR      = process.env.REACT_APP_COLOR_PRIMARY;
const SUCCESS_COLOR      = process.env.REACT_APP_COLOR_SUCCESS;
const DANGER_COLOR       = process.env.REACT_APP_COLOR_DANGER;
const WARNING_COLOR      = process.env.REACT_APP_COLOR_WARNING;
const LIGHT_COLOR        = process.env.REACT_APP_COLOR_LIGHT;
const DARK_COLOR         = process.env.REACT_APP_COLOR_DARK;
const API_URL_CHECK      = process.env.REACT_APP_API_URL_CHECK;
const API_URL_MATCH_GET  = process.env.REACT_APP_API_URL_MATCH_GET;
const MATCHES_PER_COUPON = parseInt(process.env.REACT_APP_MATCHES_PER_COUPON);
const modelCoupon        = new Model('coupon');
const modelMatch         = new Model('match');
const ModelCron          = new Model('cron');
export const PageHome    = () => {
    const [baseError, setBaseError]            = useState(null);
    const [baseLoading, setBaseLoading]        = useState(true); // Loading state
    const [coupons, setCoupons]                = useState([]);
    const [activeIndex, setActiveIndex]        = useState(null);
    const [totalEarnings, setTotalEarnings]    = useState(0);
    const [totalSpent, setTotalSpent]          = useState(0);
    const [searchParams]                       = useSearchParams();
    const xMatchCheckDataFetch                 = async (url) => {
        try {
            const response = await fetch(url);
            return await response.json();
        }
        catch (error) {
            console.error('Error fetching matches data:', error);
            return null;
        }
    };
    const xMatchCheckDataUpdateStatus          = (dbMatch, apiMatch) => {
        const homeScore = apiMatch.sc.ht.r ?? '-';
        const awayScore = apiMatch.sc.at.r ?? '-';
        const score     = `${homeScore}/${awayScore}`;
        let status      = 'Pending';
        if (homeScore !== '-' && awayScore !== '-') {
            if ((dbMatch.outcomeName === '1' && homeScore > awayScore) ||
                (dbMatch.outcomeName === '0' && homeScore === awayScore) ||
                (dbMatch.outcomeName === '2' && homeScore < awayScore)) {
                status = 'Win';
            }
            else {
                status = 'Lost';
            }
        }
        modelMatch.Update({
                              id  : dbMatch.id,
                              data: {status, score}
                          });
    };
    const xMatchCheck                          = async () => {
        const todayMatches     = await xMatchCheckDataFetch(`${API_URL_CHECK}${moment().format('YYYY-MM-DD')}`);
        const yesterdayMatches = await xMatchCheckDataFetch(`${API_URL_CHECK}${moment().subtract(1, 'days').format('YYYY-MM-DD')}`);
        if (todayMatches) {
            const matchMap = new Map(todayMatches.data.matches.map(match => [match.sgId, match]));
            modelMatch.GetAll({
                                  callBackSuccess: response => {
                                      response.forEach(dbMatch => {
                                          const apiMatch = matchMap.get(dbMatch.eventId);
                                          if (apiMatch) {
                                              xMatchCheckDataUpdateStatus(dbMatch, apiMatch);
                                          }
                                      });
                                  },
                                  callBackError  : error => {
                                      console.error('Error fetching pending matches:', error);
                                  }
                              });
        }
        if (yesterdayMatches) {
            const matchMap = new Map(yesterdayMatches.data.matches.map(match => [match.sgId, match]));
            modelMatch.GetAll({
                                  queryParams    : [{field: 'status', operator: '==', value: 'Pending'}],
                                  callBackSuccess: response => {
                                      response.forEach(dbMatch => {
                                          const apiMatch = matchMap.get(dbMatch.eventId);
                                          if (apiMatch) {
                                              xMatchCheckDataUpdateStatus(dbMatch, apiMatch);
                                          }
                                      });
                                  },
                                  callBackError  : error => {
                                      console.error('Error fetching pending matches:', error);
                                  }
                              });
        }
    };
    // ->
    const xMatchGenerateCheckMatchesInDB       = async (matches) => {
        const availableMatches = [];
        for (const match of matches) {
            const exists = await xMatchGenerateCheckMatchesInDBExists(match.eventName);
            if (!exists) {
                availableMatches.push(match);
            }
        }
        return availableMatches;
    };
    const xMatchGenerateCheckMatchesInDBExists = (eventName) => {
        return new Promise((resolve, reject) => {
            modelMatch.GetAll({
                                  queryParams    : [{field: 'eventName', operator: '==', value: eventName}],
                                  callBackSuccess: response => {
                                      resolve(response.length > 0);
                                  },
                                  callBackError  : error => {
                                      reject(error);
                                  }
                              });
        });
    };
    const xMatchGenerateGetCreateCoupons       = (matches) => {
        const coupons = [];
        while (matches.length >= MATCHES_PER_COUPON) {
            const coupon = matches.splice(0, MATCHES_PER_COUPON);
            coupons.push(coupon);
        }
        return coupons;
    };
    const xMatchGenerateGenerateUniqueCouponId = () => {
        return 'coupon_' + Math.random().toString(36).substr(2, 9);
    };
    const xMatchGenerateSaveCouponToDb         = (coupon, couponId) => {
        const eventIds = coupon.map(match => match.eventId);
        modelCoupon.Create({
                               data           : {couponId, eventIds, status: 'Pending', created_at: moment().format()},
                               callBackSuccess: () => {
                                   coupon.forEach(match => {
                                       modelMatch.Create({
                                                             data           : {...match, couponId, status: 'Pending', score: '-/-'},
                                                             callBackSuccess: () => {
                                                                 console.log('Match successfully created:', match.eventId, 'Coupon ID:', couponId);
                                                             },
                                                             callBackError  : error => {
                                                                 console.error('Error creating match:', error);
                                                             }
                                                         });
                                   });
                               },
                               callBackError  : error => {
                                   console.error('Error creating coupon:', error);
                               }
                           });
    };
    const xMatchGenerate                       = async () => {
        const matchesData = await xMatchCheckDataFetch(API_URL_MATCH_GET);
        if (matchesData) {
            const matches = matchesData.data.filter(data => data.marketName === 'Maç Sonucu');
            if (matches.length < MATCHES_PER_COUPON) {
                console.error('Not enough match data available.');
                return;
            }
            try {
                const availableMatches = await xMatchGenerateCheckMatchesInDB(matches);
                const coupons          = xMatchGenerateGetCreateCoupons(availableMatches);
                coupons.forEach(coupon => {
                    const couponId = xMatchGenerateGenerateUniqueCouponId();
                    xMatchGenerateSaveCouponToDb(coupon, couponId);
                });
            }
            catch (error) {
                console.error('Error checking matches in the database:', error);
            }
        }
        else {
            console.error('Error fetching data from API.');
        }
    };
    // ->
    const xMatchShow                           = () => {
        modelCoupon.GetAll({
                               orderParams    : [{field: 'created_at', direction: 'desc'}],
                               callBackSuccess: async (couponData) => {
                                   const enrichedCoupons = await Promise.all(
                                       couponData.map(async (coupon) => {
                                           return new Promise((resolve, reject) => {
                                               modelMatch.GetAll({
                                                                     queryParams    : [{field: 'couponId', operator: '==', value: coupon.couponId}],
                                                                     callBackSuccess: (matches) => {
                                                                         const totalOdds     = matches.reduce((acc, match) => acc * parseFloat(match.odd), 1).toFixed(2);
                                                                         const allMatchesWon = matches.every(match => match.status === 'Win');
                                                                         resolve({...coupon, matches, totalOdds, allMatchesWon});
                                                                     },
                                                                     callBackError  : (error) => {
                                                                         console.error('Error fetching match data:', error);
                                                                         reject(error);
                                                                     }
                                                                 });
                                           });
                                       })
                                   );
                                   // Kupon durumunu belirleme
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
                                   setBaseLoading(false); // Set loading to false when data is loaded
                               },
                               callBackError  : (error) => {
                                   console.error('Error fetching coupon data:', error);
                                   setBaseError('Error fetching coupon data.');
                                   setBaseLoading(false); // Set loading to false in case of error
                               }
                           });
    };
    const xMatchShowToggleDetails              = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
        setCoupons((prevCoupons) =>
                       prevCoupons.map((coupon, i) => ({
                           ...coupon,
                           showDetails: i === index ? !coupon.showDetails : false
                       }))
        );
    };
    const xMatchShowFormatDate                 = (dateString) => {
        const options = {year: '2-digit', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'};
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };
    const xMatchShowFormatMoney                = (moneyString) => {
        return moneyString.toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'}).replace('TRY', '');
    };
    useEffect(() => {
        const xMatchGetCron = () => {
            const lastUpdate = searchParams.get('lastUpdate');
            if (lastUpdate !== null) {
                ModelCron.Create({lastUpdate: lastUpdate});
            }
        };
        xMatchGetCron();
        xMatchGenerate();
        xMatchCheck();
        xMatchShow();
    }, []);
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
                                LOADING
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
                                SYSTEM ERROR
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
    const getBalance      = totalEarnings - totalSpent;
    const getBalanceColor = getBalance >= 0 ? SUCCESS_COLOR : DANGER_COLOR;
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
                            backgroundColor: getBalanceColor,
                            color          : 'white',
                            borderRadius   : '5px',
                            padding        : '2px 5px',
                            minWidth       : '120px'
                        }}>
                            {xMatchShowFormatMoney(getBalance)}
                        </div>
                    </div>
                </div>
                <div>
                    <div><img src={require('./image-header.webp')} style={{width: '100%', height: '200px', objectFit: 'cover', marginBottom: '10px', borderRadius: '5px'}} alt="Xsh Bet Simulator"/></div>
                </div>
                {coupons.map((coupon, index) => (
                    <div key={coupon.couponId} style={{
                        marginBottom: '5px',
                        border      : `1px solid ${DARK_COLOR}`,
                        borderRadius: '5px',
                        transition  : 'margin 0.3s'
                    }}>
                        <div onClick={() => xMatchShowToggleDetails(index)} style={{
                            cursor         : 'pointer',
                            padding        : '10px',
                            backgroundColor: LIGHT_COLOR,
                            display        : 'flex',
                            justifyContent : 'space-between',
                            alignItems     : 'center'
                        }}>
                            <div style={{marginRight: 'auto'}}>{moment(coupon.created_at).format('DD-MM-YY HH:mm')}</div>
                            {coupon.allMatchesWon && (
                                <div style={{textAlign: 'right', marginRight: '10px'}}>
                                    {xMatchShowFormatMoney(coupon.totalOdds * 1000)} TL
                                </div>
                            )}
                            <div style={{
                                textAlign      : 'center',
                                backgroundColor: coupon.status === 'Win' ? SUCCESS_COLOR : (coupon.status === 'Lost' ? DANGER_COLOR : WARNING_COLOR),
                                color          : 'white',
                                borderRadius   : '5px',
                                padding        : '2px 5px',
                                marginRight    : '10px',
                                minWidth       : '65px'
                            }}>
                                {coupon.status}
                            </div>
                            <div style={{width: '40px', textAlign: 'center', backgroundColor: PRIMARY_COLOR, color: 'white', borderRadius: '5px', padding: '2px 2px', margin: '0'}}>{coupon.totalOdds}</div>
                        </div>
                        {coupon.showDetails && (
                            <div style={{backgroundColor: '#FFFFFF', borderTop: `1px solid ${DARK_COLOR}`}}>
                                {coupon.matches.map((match, matchIndex) => {
                                    const isLastRow = matchIndex === coupon.matches.length - 1;
                                    return (
                                        <div key={match.eventId} style={{
                                            borderBottom  : isLastRow ? 'unset' : `1px solid ${DARK_COLOR}`,
                                            padding       : '10px',
                                            display       : 'flex',
                                            justifyContent: 'space-between',
                                            alignItems    : 'center'
                                        }}>
                                            <div style={{marginRight: 'auto', textOverflow: 'ellipsis', whiteSpace: 'nowrap', overflow: 'hidden', maxWidth: '200px'}}>{match.eventName}</div>
                                            <div style={{width: '125px', textAlign: 'center', display: ''}} className="hide-mobile">{xMatchShowFormatDate(match.eventDate)}</div>
                                            <div style={{width: '40px', textAlign: 'center'}}>{match.score}</div>
                                            <div style={{
                                                width          : '40px',
                                                textAlign      : 'center',
                                                backgroundColor: match.status === 'Win' ? SUCCESS_COLOR : (match.status === 'Lost' ? DANGER_COLOR : WARNING_COLOR),
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding        : '2px 2px',
                                                margin         : '0 2px'
                                            }}>{match.outcomeName}</div>
                                            <div style={{
                                                width          : '40px',
                                                textAlign      : 'center',
                                                backgroundColor: match.status === 'Win' ? SUCCESS_COLOR : (match.status === 'Lost' ? DANGER_COLOR : WARNING_COLOR),
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding        : '2px 2px',
                                                margin         : '0 2px'
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
