import React, {useState, useEffect} from 'react';
import {Model}                      from './Model';
const modelCoupon         = new Model('coupon');
const modelMatch          = new Model('match');
const API_URL_MATCH_GET   = process.env.REACT_APP_API_URL_MATCH_GET;
const MATCHES_PER_COUPON  = parseInt(process.env.REACT_APP_MATCHES_PER_COUPON);
/**
 * PageMatchGet component to fetch and process match data from an API.
 */
export const PageMatchGet = () => {
    /**
     * Check if matches are already in the database.
     * @param {Array} matches - List of matches to check.
     * @returns {Array} - List of matches not in the database.
     */
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
    /**
     * Check if a match exists in the database by event name.
     * @param {string} eventName - Event name to check.
     * @returns {boolean} - True if match exists, false otherwise.
     */
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
    /**
     * Create coupons from a list of matches.
     * @param {Array} matches - List of matches to create coupons from.
     * @returns {Array} - List of created coupons.
     */
    const createCoupons          = (matches) => {
        const coupons = [];
        while (matches.length >= MATCHES_PER_COUPON) {
            const coupon = matches.splice(0, MATCHES_PER_COUPON);
            coupons.push(coupon);
        }
        return coupons;
    };
    /**
     * Generate a unique coupon ID.
     * @returns {string} - Unique coupon ID.
     */
    const generateUniqueCouponId = () => {
        return 'coupon_' + Math.random().toString(36).substr(2, 9);
    };
    /**
     * Save a coupon to the database.
     * @param {Array} coupon - Coupon data to save.
     * @param {string} couponId - ID of the coupon.
     */
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
    /**
     * Fetch match data from the API and process it.
     */
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
    // Fetch match data on component mount
    useEffect(() => {
        MatchGet();
    }, []);
    return <div>PageGetMatch</div>;
};
