import React, {useEffect} from 'react';
import {Model}            from './Model';
import moment             from 'moment/moment';
const modelCoupon           = new Model('coupon');
const modelMatch            = new Model('match');
const API_URL_CHECK         = process.env.REACT_APP_API_URL_CHECK;
/**
 * PageMatchCheck component to check match results and update the database.
 */
export const PageMatchCheck = () => {
    /**
     * Fetch match data from the API and update the database.
     */
    const fetchData = () => {
        fetch(API_URL_CHECK + moment().format('YYYY-MM-DD'))
            .then(response => response.json())
            .then(function (response) {
                const matchesData = response;
                const matchMap    = new Map();
                matchesData.data.matches.forEach(match => {
                    matchMap.set(match.sgId, match);
                });
                modelMatch.GetAll({
                                      callBackSuccess: function (response) {
                                          response.forEach(function (dbMatch) {
                                              const apiMatch = matchMap.get(dbMatch.eventId);
                                              if (apiMatch) {
                                                  let homeScore = apiMatch.sc.ht.r;
                                                  let awayScore = apiMatch.sc.at.r;
                                                  homeScore     = homeScore === undefined ? '-' : homeScore;
                                                  awayScore     = awayScore === undefined ? '-' : awayScore;
                                                  const score   = homeScore + '/' + awayScore;
                                                  let status    = 'Pending';
                                                  if (homeScore !== '-' && awayScore !== '-') {
                                                      if (dbMatch.outcomeName === '1') {
                                                          if (homeScore > awayScore) {
                                                              status = 'Win';
                                                          }
                                                          else {
                                                              status = 'Lost';
                                                          }
                                                      }
                                                      if (dbMatch.outcomeName === '0') {
                                                          if (homeScore === awayScore) {
                                                              status = 'Win';
                                                          }
                                                          else {
                                                              status = 'Lost';
                                                          }
                                                      }
                                                      if (dbMatch.outcomeName === '2') {
                                                          if (homeScore < awayScore) {
                                                              status = 'Win';
                                                          }
                                                          else {
                                                              status = 'Lost';
                                                          }
                                                      }
                                                  }
                                                  modelMatch.Update({
                                                                        id  : dbMatch.id,
                                                                        data: {
                                                                            status: status,
                                                                            score : score
                                                                        }
                                                                    });
                                              }
                                          });
                                      },
                                      callBackError  : function (error) {
                                          console.error('Error fetching pending matches:', error);
                                      }
                                  });
            });
        fetch(API_URL_CHECK + moment().add(-1, 'days').format('YYYY-MM-DD'))
            .then(response => response.json())
            .then(function (response) {
                const matchesData = response;
                const matchMap    = new Map();
                matchesData.data.matches.forEach(match => {
                    matchMap.set(match.sgId, match);
                });
                modelMatch.GetAll({
                                      queryParams    : [{field: 'status', operator: '==', value: 'Pending'}],
                                      callBackSuccess: function (response) {
                                          response.forEach(function (dbMatch) {
                                              const apiMatch = matchMap.get(dbMatch.eventId);
                                              if (apiMatch) {
                                                  const homeScore = apiMatch.sc.ht.r;
                                                  const awayScore = apiMatch.sc.at.r;
                                                  const score     = homeScore + '/' + awayScore;
                                                  var status      = 'Pending';
                                                  if (dbMatch.outcomeName === '1') {
                                                      if (homeScore > awayScore) {
                                                          status = 'Win';
                                                      }
                                                      else {
                                                          status = 'Lost';
                                                      }
                                                  }
                                                  if (dbMatch.outcomeName === '0') {
                                                      if (homeScore === awayScore) {
                                                          status = 'Win';
                                                      }
                                                      else {
                                                          status = 'Lost';
                                                      }
                                                  }
                                                  if (dbMatch.outcomeName === '2') {
                                                      if (homeScore < awayScore) {
                                                          status = 'Win';
                                                      }
                                                      else {
                                                          status = 'Lost';
                                                      }
                                                  }
                                                  modelMatch.Update({
                                                                        id  : dbMatch.id,
                                                                        data: {
                                                                            status: status,
                                                                            score : score
                                                                        }
                                                                    });
                                              }
                                          });
                                      },
                                      callBackError  : function (error) {
                                          console.error('Error fetching pending matches:', error);
                                      }
                                  });
            });
    };
    // Fetch data on component mount
    useEffect(() => {
        fetchData();
    }, []);
    return (
        <div>PageMatchCheck</div>
    );
};
