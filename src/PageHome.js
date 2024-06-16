import React, {useState, useEffect} from 'react';
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
const couponModel        = new Model('coupon');
const matchModel         = new Model('match');
export const PageHome    = () => {
    const [coupons, setCoupons]             = useState([]);
    const [error, setError]                 = useState(null);
    const [loading, setLoading]           = useState(true); // Loading state
    const [activeIndex, setActiveIndex]     = useState(null);
    const [totalEarnings, setTotalEarnings] = useState(0);
    const [totalSpent, setTotalSpent]       = useState(0);
    const xMatchDataFetch                 = async (url) => {
        try {
            const response = await fetch(url);
            return await response.json();
        }
        catch (error) {
            console.error('Error fetching matches data:', error);
            return null;
        }
    };
    const xMatchDataUpdateStatus          = (dbMatch, apiMatch) => {
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
        matchModel.Update({
                              id  : dbMatch.id,
                              data: {status, score}
                          });
    };
    const xMatchCheck                     = async () => {
        const todayMatches     = await xMatchDataFetch(`${API_URL_CHECK}${moment().format('YYYY-MM-DD')}`);
        const yesterdayMatches = await xMatchDataFetch(`${API_URL_CHECK}${moment().subtract(1, 'days').format('YYYY-MM-DD')}`);
        if (todayMatches) {
            const matchMap = new Map(todayMatches.data.matches.map(match => [match.sgId, match]));
            matchModel.GetAll({
                                  callBackSuccess: response => {
                                      response.forEach(dbMatch => {
                                          const apiMatch = matchMap.get(dbMatch.eventId);
                                          if (apiMatch) {
                                              xMatchDataUpdateStatus(dbMatch, apiMatch);
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
            matchModel.GetAll({
                                  queryParams    : [{field: 'status', operator: '==', value: 'Pending'}],
                                  callBackSuccess: response => {
                                      response.forEach(dbMatch => {
                                          const apiMatch = matchMap.get(dbMatch.eventId);
                                          if (apiMatch) {
                                              xMatchDataUpdateStatus(dbMatch, apiMatch);
                                          }
                                      });
                                  },
                                  callBackError  : error => {
                                      console.error('Error fetching pending matches:', error);
                                  }
                              });
        }
    };
    const xMatchGetCheckMatchesInDB       = async (matches) => {
        const availableMatches = [];
        for (const match of matches) {
            const exists = await xMatchGetCheckMatchesInDBExists(match.eventName);
            if (!exists) {
                availableMatches.push(match);
            }
        }
        return availableMatches;
    };
    const xMatchGetCheckMatchesInDBExists = (eventName) => {
        return new Promise((resolve, reject) => {
            matchModel.GetAll({
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
    const xMatchGetCreateCoupons          = (matches) => {
        const coupons = [];
        while (matches.length >= MATCHES_PER_COUPON) {
            const coupon = matches.splice(0, MATCHES_PER_COUPON);
            coupons.push(coupon);
        }
        return coupons;
    };
    const xMatchGetGenerateUniqueCouponId = () => {
        return 'coupon_' + Math.random().toString(36).substr(2, 9);
    };
    const xMatchGetSaveCouponToDb         = (coupon, couponId) => {
        const eventIds = coupon.map(match => match.eventId);
        couponModel.Create({
                               data           : {couponId, eventIds, status: 'Pending', created_at: moment().format()},
                               callBackSuccess: () => {
                                   coupon.forEach(match => {
                                       matchModel.Create({
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
    const xMatchGet                       = async () => {
        const matchesData = await xMatchDataFetch(API_URL_MATCH_GET);
        if (matchesData) {
            const matches = matchesData.data.filter(data => data.marketName === 'Maç Sonucu');
            if (matches.length < MATCHES_PER_COUPON) {
                console.error('Not enough match data available.');
                return;
            }
            try {
                const availableMatches = await xMatchGetCheckMatchesInDB(matches);
                const coupons          = xMatchGetCreateCoupons(availableMatches);
                coupons.forEach(coupon => {
                    const couponId = xMatchGetGenerateUniqueCouponId();
                    xMatchGetSaveCouponToDb(coupon, couponId);
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
    const xMatchShow                      = () => {
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
                                                                         console.error('Error fetching match data:', error);
                                                                         reject(error);
                                                                     }
                                                                 });
                                           });
                                       })
                                   );
                                   const earnings        = enrichedCoupons.reduce((acc, coupon) => coupon.allMatchesWon ? acc + (coupon.totalOdds * 1000) : acc, 0);
                                   const spent           = enrichedCoupons.length * 1000;
                                   setCoupons(enrichedCoupons);
                                   setTotalEarnings(earnings);
                                   setTotalSpent(spent);
                                   setLoading(false); // Set loading to false when data is loaded
                               },
                               callBackError  : (error) => {
                                   console.error('Error fetching coupon data:', error);
                                   setError('Error fetching coupon data.');
                                   setLoading(false); // Set loading to false in case of error
                               }
                           });
    };
    const xMatchShowToggleDetails         = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
        setCoupons((prevCoupons) =>
                       prevCoupons.map((coupon, i) => ({
                           ...coupon,
                           showDetails: i === index ? !coupon.showDetails : false
                       }))
        );
    };
    const xMatchShowFormatDate            = (dateString) => {
        const options = {year: '2-digit', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'};
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };
    useEffect(() => {
        xMatchGet();
        xMatchCheck();
        xMatchShow();
    }, []);
    if (loading) {
        return (
            <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
                <div style={{width: '100%', maxWidth: '800px'}}>
                    <div style={{
                        padding        : '20px 10px',
                        marginBottom   : '10px',
                        backgroundColor: LIGHT_COLOR,
                        border         : `1px solid ${DARK_COLOR}`,
                        display        : 'flex',
                        justifyContent : 'space-between',
                        alignItems     : 'center',
                        borderRadius   : '5px'
                    }}>
                        <div>Xsh Bet Simulator</div>
                        <div>
                            <div style={{color: DARK_COLOR}}>
                                <FontAwesomeIcon icon={Fa.faSpinner} spin/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
    if (error) {
        return (
            <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
                <div style={{width: '100%', maxWidth: '800px'}}>
                    <div style={{
                        padding        : '20px 10px',
                        marginBottom   : '10px',
                        backgroundColor: LIGHT_COLOR,
                        border         : `1px solid ${DARK_COLOR}`,
                        display        : 'flex',
                        justifyContent : 'space-between',
                        alignItems     : 'center',
                        borderRadius   : '5px'
                    }}>
                        <div>Xsh Bet Simulator</div>
                        <div>
                            <div style={{color: DARK_COLOR}}>
                                Error
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
    return (
        <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
            <div style={{width: '100%', maxWidth: '800px'}}>
                <div style={{
                    padding        : '20px 10px',
                    marginBottom   : '10px',
                    backgroundColor: LIGHT_COLOR,
                    border         : `1px solid ${DARK_COLOR}`,
                    display        : 'flex',
                    justifyContent : 'space-between',
                    alignItems     : 'center',
                    borderRadius   : '5px'
                }}>
                    <div>Xsh Bet Simulator</div>
                    <div>
                        <div style={{color: DARK_COLOR}}>
                            {(totalEarnings - totalSpent).toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'}).replace('TRY', ' TRY')}
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
                                <div style={{textAlign: 'center', backgroundColor: SUCCESS_COLOR, color: 'white', borderRadius: '5px', padding: '2px 5px', marginRight: '10px'}}>
                                    {(coupon.totalOdds * 1000).toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'})}
                                </div>
                            )}
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
