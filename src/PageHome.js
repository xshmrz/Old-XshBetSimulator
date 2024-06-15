import React, {useState, useEffect} from 'react';
import {Model}                      from './Model';
import moment                       from 'moment/moment';
const PRIMARY_COLOR   = process.env.REACT_APP_COLOR_PRIMARY;
const SUCCESS_COLOR   = process.env.REACT_APP_COLOR_SUCCESS;
const DANGER_COLOR    = process.env.REACT_APP_COLOR_DANGER;
const WARNING_COLOR   = process.env.REACT_APP_COLOR_WARNING;
const LIGHT_COLOR     = process.env.REACT_APP_COLOR_LIGHT;
const DARK_COLOR      = process.env.REACT_APP_COLOR_DARK;
const couponModel     = new Model('coupon');
const matchModel      = new Model('match');
export const PageHome = () => {
    const [coupons, setCoupons]             = useState([]);
    const [error, setError]                 = useState(null);
    const [activeIndex, setActiveIndex]     = useState(null);
    const [totalEarnings, setTotalEarnings] = useState(0);
    const [totalSpent, setTotalSpent]       = useState(0);
    useEffect(() => {
        fetchCoupons();
    }, []);
    const fetchCoupons  = () => {
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
                               },
                               callBackError  : (error) => {
                                   console.error('Error fetching coupon data:', error);
                                   setError('Error fetching coupon data.');
                               }
                           });
    };
    const toggleDetails = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
        setCoupons((prevCoupons) =>
                       prevCoupons.map((coupon, i) => ({
                           ...coupon,
                           showDetails: i === index ? !coupon.showDetails : false
                       }))
        );
    };
    const formatDate    = (dateString) => {
        const options = {year: '2-digit', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'};
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };
    if (error) {
        return <div>{error}</div>;
    }
    return (
        <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
            <div style={{width: '100%', maxWidth: '800px'}}>
                <div
                    style={{
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
                        <div style={{fontWeight: 'bold', color: SUCCESS_COLOR}}>+ {totalEarnings.toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'})}</div>
                        <div style={{fontWeight: 'bold', color: DANGER_COLOR}}>- {totalSpent.toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'})}</div>
                    </div>
                </div>
                <div>
                    <div><img src={require('./image-header.webp')} style={{width: '100%', height: '200px', objectFit: 'cover', marginBottom: '10px', borderRadius: '5px'}}/></div>
                </div>
                {coupons.map((coupon, index) => (
                    <div
                        key={coupon.couponId}
                        style={{
                            marginBottom: '5px',
                            border      : `1px solid ${DARK_COLOR}`,
                            borderRadius: '5px',
                            transition  : 'margin 0.3s'
                        }}>
                        <div
                            onClick={() => toggleDetails(index)}
                            style={{
                                cursor         : 'pointer',
                                padding        : '10px',
                                backgroundColor: LIGHT_COLOR,
                                display        : 'flex',
                                justifyContent : 'space-between',
                                alignItems     : 'center'
                            }}>
                            <div style={{marginRight: 'auto'}}>{coupon.couponId.toUpperCase().replace('COUPON_', '')}</div>
                            {coupon.allMatchesWon && (
                                <div style={{textAlign: 'center', backgroundColor: SUCCESS_COLOR, color: 'white', borderRadius: '5px', padding: '2px 5px', marginRight: '10px'}}>{(coupon.totalOdds * 1000).toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'})}</div>
                            )}
                            <div style={{textAlign: 'right', marginRight: '10px', fontSize: '9px'}}>{moment(coupon.created_at).format('DD-MM-YY HH:mm')}</div>
                            <div style={{width: '40px', textAlign: 'center', backgroundColor: PRIMARY_COLOR, color: 'white', borderRadius: '5px', padding: '2px 2px', margin: '0 2px'}}>{coupon.totalOdds}</div>
                        </div>
                        {coupon.showDetails && (
                            <div style={{backgroundColor: '#FFFFFF', borderTop: `1px solid ${DARK_COLOR}`}}>
                                {coupon.matches.map((match, matchIndex) => {
                                    const isLastRow = matchIndex === coupon.matches.length - 1;
                                    return (
                                        <div
                                            key={match.eventId}
                                            style={{
                                                borderBottom: isLastRow ? 'unset' : `1px solid ${DARK_COLOR}`,
                                                padding       : '10px',
                                                display       : 'flex',
                                                justifyContent: 'space-between',
                                                alignItems    : 'center'
                                            }}>
                                            <div style={{marginRight: 'auto', textOverflow: 'ellipsis', whiteSpace: 'nowrap', overflow: 'hidden', maxWidth: '200px'}}>{match.eventName}</div>
                                            <div style={{width: '125px', textAlign: 'center', display: ''}} className="hide-mobile">{formatDate(match.eventDate)}</div>
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
