import React, {useState, useEffect} from 'react';
import {Model}                      from './Model';
const COLOR_PRIMARY   = '#007BFF';
const COLOR_SUCCESS   = '#28A745';
const COLOR_DANGER    = '#DC3545';
const COLOR_WARNING   = '#8C6600';
const COLOR_LIGHT     = '#D9E1EC';
const COLOR_DARK      = '#343A40';
const modelCoupon     = new Model('coupon');
const modelMatch      = new Model('match');
export const PageHome = () => {
    const [coupons, setCoupons]             = useState([]);
    const [error, setError]                 = useState(null);
    const [totalSpent, setTotalSpent]       = useState(0);
    const [totalEarnings, setTotalEarnings] = useState(0);
    const [totalProfit, setTotalProfit]     = useState(0);
    const [activeIndex, setActiveIndex]     = useState(null);
    useEffect(() => {
        fetchCoupons();
    }, []);
    const fetchCoupons    = () => {
        modelCoupon.GetAll({
                               callBackSuccess: async (couponData) => {
                                   const enrichedCoupons = await Promise.all(
                                       couponData.map(async (coupon) => {
                                           return new Promise((resolve, reject) => {
                                               modelMatch.GetAll({
                                                                     queryParams    : [{field: 'couponId', operator: '==', value: coupon.couponId}],
                                                                     callBackSuccess: (matches) => {
                                                                         const totalOdds = matches.reduce((acc, match) => acc * parseFloat(match.odd), 1).toFixed(2);
                                                                         resolve({...coupon, matches, totalOdds});
                                                                     },
                                                                     callBackError  : (error) => {
                                                                         console.error('Error fetching match data:', error);
                                                                         reject(error);
                                                                     }
                                                                 });
                                           });
                                       })
                                   );
                                   setCoupons(enrichedCoupons);
                                   calculateTotals(enrichedCoupons);
                               },
                               callBackError  : (error) => {
                                   console.error('Error fetching coupon data:', error);
                                   setError('Error fetching coupon data.');
                               }
                           });
    };
    const calculateTotals = (coupons) => {
        const totalSpent    = (coupons.length * 1000).toFixed(2);
        const totalEarnings = coupons.reduce((acc, coupon) => acc + 1000 * parseFloat(coupon.totalOdds), 0).toFixed(2);
        const totalProfit   = (totalEarnings - totalSpent).toFixed(2);
        setTotalSpent(totalSpent);
        setTotalEarnings(totalEarnings);
        setTotalProfit(totalProfit);
    };
    const toggleDetails   = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
        setCoupons((prevCoupons) =>
                       prevCoupons.map((coupon, i) => ({
                           ...coupon,
                           showDetails: i === index ? !coupon.showDetails : false
                       }))
        );
    };
    const formatDate      = (dateString) => {
        const options = {year: '2-digit', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'};
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };
    if (error) {
        return <div>{error}</div>;
    }
    return (
        <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '12px'}}>
            <div style={{width: '800px'}}>
                <div
                    style={{
                        padding        : '20px 10px',
                        marginBottom   : '10px',
                        backgroundColor: COLOR_LIGHT,
                        border         : `1px solid ${COLOR_DARK}`,
                        display        : 'flex',
                        justifyContent : 'space-between',
                        alignItems     : 'center'
                    }}>
                    <div>App Name</div>
                    <div>{totalSpent} / {totalEarnings} / {totalProfit}</div>
                </div>
                {coupons.map((coupon, index) => (
                    <div
                        key={coupon.couponId}
                        style={{
                            marginBottom: '5px',
                            border      : `1px solid ${COLOR_DARK}`,
                            transition  : 'margin 0.3s'
                        }}>
                        <div
                            onClick={() => toggleDetails(index)}
                            style={{
                                cursor         : 'pointer',
                                padding        : '10px',
                                backgroundColor: COLOR_LIGHT,
                                display        : 'flex',
                                justifyContent : 'space-between',
                                alignItems     : 'center'
                            }}>
                            <div style={{marginRight: 'auto'}}>Coupon ID: {coupon.couponId}</div>
                            <div style={{textAlign: 'right', marginRight: '10px'}}>{(coupon.totalOdds * 1000).toFixed(2)} TL</div>
                            <div style={{width: '75px', textAlign: 'center', backgroundColor: COLOR_PRIMARY, color: 'white', borderRadius: '5px', padding: '2px 2px', margin: '0 2px'}}>{coupon.totalOdds}</div>
                        </div>
                        {coupon.showDetails && (
                            <div style={{backgroundColor: '#FFFFFF', borderTop: `1px solid ${COLOR_DARK}`}}>
                                {coupon.matches.map((match, matchIndex) => {
                                    const isLastRow = matchIndex === coupon.matches.length - 1;
                                    return (
                                        <div
                                            key={match.eventId}
                                            style={{
                                                borderBottom  : isLastRow ? 'unset' : `1px solid ${COLOR_DARK}`,
                                                padding       : '10px',
                                                display       : 'flex',
                                                justifyContent: 'space-between',
                                                alignItems    : 'center'
                                            }}>
                                            <div style={{marginRight: 'auto'}}>{match.eventName}</div>
                                            <div style={{width: '125px', textAlign: 'center'}}>{formatDate(match.eventDate)}</div>
                                            <div style={{width: '75px', textAlign: 'center'}}>{match.score}</div>
                                            <div style={{
                                                width          : '75px',
                                                textAlign      : 'center',
                                                backgroundColor: match.status === 'Win' ? COLOR_SUCCESS : (match.status === 'Lost' ? COLOR_DANGER : COLOR_WARNING),
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding        : '2px 2px',
                                                margin         : '0 2px'
                                            }}>{match.status}</div>
                                            <div style={{
                                                width          : '75px',
                                                textAlign      : 'center',
                                                backgroundColor: match.status === 'Win' ? COLOR_SUCCESS : (match.status === 'Lost' ? COLOR_DANGER : COLOR_WARNING),
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding        : '2px 2px',
                                                margin         : '0 2px'
                                            }}>{match.outcomeName}</div>
                                            <div style={{
                                                width          : '75px',
                                                textAlign      : 'center',
                                                backgroundColor: COLOR_PRIMARY,
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
