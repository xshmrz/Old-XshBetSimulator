import React, {useEffect, useState} from 'react';
import {Model}                      from './Model';
import moment                       from 'moment/moment';
const matchModel            = new Model('match');
const API_URL_CHECK         = process.env.REACT_APP_API_URL_CHECK;
export const PageMatchCheck = () => {
    const [loading, setLoading] = useState('Loading');
    const xMatchCheck = async () => {
        await fetch(API_URL_CHECK + moment().format('YYYY-MM-DD'))
            .then(response => response.json())
            .then(response => {
                const matchesData = response;
                const matchMap    = new Map();
                matchesData.data.matches.forEach(match => {
                    matchMap.set(match.sgId, match);
                });
                matchModel.GetAll({
                                      callBackSuccess: response => {
                                          response.forEach(dbMatch => {
                                              const apiMatch = matchMap.get(dbMatch.eventId);
                                              if (apiMatch) {
                                                  let homeScore = apiMatch.sc.ht.r;
                                                  let awayScore = apiMatch.sc.at.r;
                                                  homeScore     = homeScore === undefined ? '-' : homeScore;
                                                  awayScore     = awayScore === undefined ? '-' : awayScore;
                                                  const score   = homeScore + '/' + awayScore;
                                                  let status    = 'Pending';
                                                  if (homeScore !== '-' && awayScore !== '-') {
                                                      if (dbMatch.outcomeName === '1' && homeScore > awayScore) {
                                                          status = 'Win';
                                                      }
                                                      else if (dbMatch.outcomeName === '0' && homeScore === awayScore) {
                                                          status = 'Win';
                                                      }
                                                      else if (dbMatch.outcomeName === '2' && homeScore < awayScore) {
                                                          status = 'Win';
                                                      }
                                                      else {
                                                          status = 'Lost';
                                                      }
                                                  }
                                                  matchModel.Update({
                                                                        id  : dbMatch.id,
                                                                        data: {status: status, score: score}
                                                                    });

                                              }
                                          });
                                      },
                                      callBackError  : error => {
                                          console.error('Error fetching pending matches:', error);
                                      }
                                  });
            });
        await fetch(API_URL_CHECK + moment().add(-1, 'days').format('YYYY-MM-DD'))
            .then(response => response.json())
            .then(response => {
                const matchesData = response;
                const matchMap    = new Map();
                matchesData.data.matches.forEach(match => {
                    matchMap.set(match.sgId, match);
                });
                matchModel.GetAll({
                                      queryParams    : [{field: 'status', operator: '==', value: 'Pending'}],
                                      callBackSuccess: response => {
                                          response.forEach(dbMatch => {
                                              const apiMatch = matchMap.get(dbMatch.eventId);
                                              if (apiMatch) {
                                                  const homeScore = apiMatch.sc.ht.r;
                                                  const awayScore = apiMatch.sc.at.r;
                                                  const score     = homeScore + '/' + awayScore;
                                                  let status = 'Pending';
                                                  if (dbMatch.outcomeName === '1' && homeScore > awayScore) {
                                                      status = 'Win';
                                                  }
                                                  else if (dbMatch.outcomeName === '0' && homeScore === awayScore) {
                                                      status = 'Win';
                                                  }
                                                  else if (dbMatch.outcomeName === '2' && homeScore < awayScore) {
                                                      status = 'Win';
                                                  }
                                                  else {
                                                      status = 'Lost';
                                                  }
                                                  matchModel.Update({
                                                                        id  : dbMatch.id,
                                                                        data: {status: status, score: score}
                                                                    });
                                              }
                                          });
                                      },
                                      callBackError  : error => {
                                          console.error('Error fetching pending matches:', error);
                                      }
                                  });
            })
            .then(() => {
            });
    };
    useEffect(() => {
        xMatchCheck();
    }, []);
    return <div>PageMatchCheck : {loading}</div>;
};
