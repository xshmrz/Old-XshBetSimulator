import React, {useState, useEffect} from 'react';
import {Model}                      from './Model';
const modelCoupon         = new Model('coupon');
const modelMatch          = new Model('match');
const API_URL_MATCH_GET   = 'https://sportsbook.iddaa.com/SportsBook/getPopulerBets?sportId=1&limit=40';
const MATCHES_PER_COUPON  = 4;
export const PageMatchGet = () => {
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
            modelMatch.GetAll({
                                  queryParams    : [{field: 'eventName', operator: '==', value: eventName}],
                                  callBackSuccess: function (response) {
                                      resolve(response.length > 0);
                                  },
                                  callBackError  : function (error) {
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
        modelCoupon.Create({
                               data           : {couponId: couponId, eventIds: eventIds, status: 'Pending'},
                               callBackSuccess: function () {
                                   console.log('Coupon successfully created:', couponId);
                                   coupon.forEach(match => {
                                       modelMatch.Create({
                                                             data           : {...match, couponId: couponId, status: 'Pending', score: '- / -'},
                                                             callBackSuccess: function () {
                                                                 console.log('Match successfully created:', match.eventId, 'Coupon ID:', couponId);
                                                             },
                                                             callBackError  : function (error) {
                                                                 console.error('Error creating match:', error);
                                                             }
                                                         });
                                   });
                               },
                               callBackError  : function (error) {
                                   console.error('Error creating coupon:', error);
                               }
                           });
    };
    const MatchGet               = () => {
        fetch(API_URL_MATCH_GET)
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
                    .catch(error => {
                        console.error('Error checking matches in the database:', error);
                    });
            })
            .catch(error => {
                console.error('Error fetching data from API:', error);
            });
    };
    useEffect(() => {
        MatchGet();
    }, []);
    return <div>PageGetMatch</div>;
};
