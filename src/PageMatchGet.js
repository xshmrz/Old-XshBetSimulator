import React, {useState, useEffect} from 'react';
import {Model}                      from './Model';
import moment                       from 'moment';
const couponModel         = new Model('coupon');
const matchModel          = new Model('match');
const API_URL_MATCH_GET   = process.env.REACT_APP_API_URL_MATCH_GET;
const MATCHES_PER_COUPON  = parseInt(process.env.REACT_APP_MATCHES_PER_COUPON);
export const PageMatchGet = () => {
    const [loading, setLoading]  = useState('Loading');
    const checkMatchesInDB       = async (matches) => {
        const availableMatches = [];
        for (const match of matches) {
            const exists = await checkMatchExists(match.eventName);
            if (!exists) {
                availableMatches.push(match);
            }
        }
        return availableMatches;
    };
    const checkMatchExists       = async (eventName) => {
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
    const createCoupons          = (matches) => {
        const coupons = [];
        while (matches.length >= MATCHES_PER_COUPON) {
            const coupon = matches.splice(0, MATCHES_PER_COUPON);
            coupons.push(coupon);
        }
        return coupons;
    };
    const generateUniqueCouponId = () => {
        return 'coupon_' + Math.random().toString(36).substr(2, 9);
    };
    const saveCouponToDB         = (coupon, couponId) => {
        const eventIds = coupon.map(match => match.eventId);
        couponModel.Create({
                               data           : {couponId: couponId, eventIds: eventIds, status: 'Pending', created_at: moment().format()},
                               callBackSuccess: () => {
                                   coupon.forEach(match => {
                                       matchModel.Create({
                                                             data           : {...match, couponId: couponId, status: 'Pending', score: '-/-'},
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
    const fetchMatchData         = async () => {
        await fetch(API_URL_MATCH_GET)
            .then(response => response.json())
            .then(response => {
                const matches = response.data.filter(data => data.marketName === 'Maç Sonucu');
                if (matches.length < MATCHES_PER_COUPON) {
                    console.error('Not enough match data available.');
                    return;
                }
                checkMatchesInDB(matches)
                    .then(availableMatches => {
                        const coupons = createCoupons(availableMatches);
                        coupons.forEach(coupon => {
                            const couponId = generateUniqueCouponId();
                            saveCouponToDB(coupon, couponId);
                        });
                    })
                    .then(() => {
                        setLoading('Complete');
                    })
                    .catch(error => {
                        console.error('Error checking matches in the database:', error);
                    });
            })
            .catch(error => {
                console.error('Error fetching data from API:', error);
            });
    };
    useEffect(() => {
        fetchMatchData();
    }, []);
    return <div>PageGetMatch : {loading}</div>;
};
