import React, {useState, useEffect} from 'react';
import {Model}                      from './Model';
import moment                       from 'moment/moment';
const COLOR_PRIMARY   = process.env.REACT_APP_COLOR_PRIMARY;
const COLOR_SUCCESS   = process.env.REACT_APP_COLOR_SUCCESS;
const COLOR_DANGER    = process.env.REACT_APP_COLOR_DANGER;
const COLOR_WARNING   = process.env.REACT_APP_COLOR_WARNING;
const COLOR_LIGHT     = process.env.REACT_APP_COLOR_LIGHT;
const COLOR_DARK      = process.env.REACT_APP_COLOR_DARK;
const modelCoupon     = new Model('coupon');
const modelMatch      = new Model('match');
/**
 * PageHome component to display the list of coupons and their details.
 */
export const PageHome = () => {
    const [coupons, setCoupons]         = useState([]);
    const [error, setError]             = useState(null);
    const [activeIndex, setActiveIndex] = useState(null);
    // Fetch coupons on component mount
    useEffect(() => {
        fetchCoupons();
    }, []);
    /**
     * Fetch all coupons from the database.
     */
    const fetchCoupons  = () => {
        modelCoupon.GetAll({
                               orderParams: [{field: 'created_at', direction: 'desc'}],
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
                               },
                               callBackError  : (error) => {
                                   console.error('Error fetching coupon data:', error);
                                   setError('Error fetching coupon data.');
                               }
                           });
    };
    /**
     * Toggle the visibility of coupon details.
     * @param {number} index - Index of the coupon to toggle.
     */
    const toggleDetails = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
        setCoupons((prevCoupons) =>
                       prevCoupons.map((coupon, i) => ({
                           ...coupon,
                           showDetails: i === index ? !coupon.showDetails : false
                       }))
        );
    };
    /**
     * Format a date string to 'tr-TR' locale.
     * @param {string} dateString - Date string to format.
     * @returns {string} - Formatted date string.
     */
    const formatDate    = (dateString) => {
        const options = {year: '2-digit', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'};
        return new Date(dateString).toLocaleDateString('tr-TR', options);
    };
    if (error) {
        return <div>{error}</div>;
    }
    // -> {(1000 * coupon.totalOdds).toLocaleString('tr-TR', {currency: 'TRY', style: 'currency', currencyDisplay: 'code'}).replace('TRY', '')}
    return (
        <div style={{display: 'flex', justifyContent: 'center', fontFamily: 'monospace', fontSize: '11px', overflowY: 'auto', height: '100vh'}}>
            <div style={{width: '100%', maxWidth: '800px'}}>
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
                    <div>Xsh Bet Simulator</div>
                </div>
                <div>
                    <div><img src={require('./image-header.webp')} style={{width: '100%', height: '200px', objectFit: 'cover', marginBottom: '10px', borderRadius: '5px'}}/></div>
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
                            <div style={{marginRight: 'auto'}}>{coupon.couponId.toUpperCase()}</div>
                            <div style={{textAlign: 'right', marginRight: '10px'}}></div>
                            <div style={{width: '40px', textAlign: 'center', backgroundColor: COLOR_PRIMARY, color: 'white', borderRadius: '5px', padding: '2px 2px', margin: '0 2px'}}>{coupon.totalOdds}</div>
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
                                            <div style={{marginRight: 'auto', textOverflow: 'ellipsis', whiteSpace: 'nowrap', overflow: 'hidden', maxWidth: '200px'}}>{match.eventName}</div>
                                            <div style={{width: '125px', textAlign: 'center', display: ''}} className="hide-mobile">{formatDate(match.eventDate)}</div>
                                            <div style={{width: '40px', textAlign: 'center'}}>{match.score}</div>
                                            <div style={{
                                                width          : '40px',
                                                textAlign      : 'center',
                                                backgroundColor: match.status === 'Win' ? COLOR_SUCCESS : (match.status === 'Lost' ? COLOR_DANGER : COLOR_WARNING),
                                                color          : 'white',
                                                borderRadius   : '5px',
                                                padding        : '2px 2px',
                                                margin         : '0 2px'
                                            }} className="">{match.outcomeName}</div>
                                            <div style={{
                                                width          : '40px',
                                                textAlign      : 'center',
                                                backgroundColor: match.status === 'Win' ? COLOR_SUCCESS : (match.status === 'Lost' ? COLOR_DANGER : COLOR_WARNING),
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
