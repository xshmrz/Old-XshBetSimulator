import {initializeApp}                                                                                                           from 'firebase/app';
import {getFirestore, collection, getDocs, addDoc, doc, getDoc, updateDoc, deleteDoc, query, where, writeBatch, orderBy, setDoc} from 'firebase/firestore';

// Firebase configuration object
const firebaseConfig = {
    apiKey           : process.env.REACT_APP_FIREBASE_API_KEY,
    authDomain       : process.env.REACT_APP_FIREBASE_AUTH_DOMAIN,
    databaseURL      : process.env.REACT_APP_FIREBASE_DATABASE_URL,
    projectId        : process.env.REACT_APP_FIREBASE_PROJECT_ID,
    storageBucket    : process.env.REACT_APP_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: process.env.REACT_APP_FIREBASE_MESSAGING_SENDER_ID,
    appId            : process.env.REACT_APP_FIREBASE_APP_ID
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const db  = getFirestore(app);

/**
 * Model class to interact with Firebase Firestore.
 * Provides methods to perform CRUD operations on a specified collection.
 */
export class Model {
    constructor(collectionName) {
        this.collectionRef = collection(db, collectionName);
    }

    /**
     * Fetch a single document by its ID.
     * @param {Object} params - Parameters for fetching the document.
     * @param {string} params.id - ID of the document to fetch.
     * @param {function} params.callBackSuccess - Callback function to call on successful fetch.
     * @param {function} params.callBackError - Callback function to call on fetch error.
     */
    async Get({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const docRef  = doc(this.collectionRef, id);
            const docSnap = await getDoc(docRef);
            if (docSnap.exists()) {
                callBackSuccess(docSnap.data());
            } else {
                callBackError('No such document!');
            }
        } catch (error) {
            callBackError(error);
        }
    }

    /**
     * Fetch all documents in the collection, optionally filtered by query parameters.
     * @param {Object} params - Parameters for fetching the documents.
     * @param {Array} [params.queryParams=[]] - Query parameters to filter the documents.
     * @param {Array} [params.orderParams=[]] - Order parameters to sort the documents.
     * @param {function} params.callBackSuccess - Callback function to call on successful fetch.
     * @param {function} params.callBackError - Callback function to call on fetch error.
     */
    async GetAll({queryParams = [], orderParams = [], callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            let q = query(this.collectionRef);
            if (queryParams.length > 0) {
                q = query(this.collectionRef, ...queryParams.map(param => where(param.field, param.operator, param.value)));
            }
            if (orderParams.length > 0) {
                orderParams.forEach(order => {
                    q = query(q, orderBy(order.field, order.direction));
                });
            }
            const querySnapshot = await getDocs(q);
            const results = [];
            querySnapshot.forEach((doc) => {
                results.push({id: doc.id, ...doc.data()});
            });
            callBackSuccess(results);
        } catch (error) {
            callBackError(error);
        }
    }

    /**
     * Create a new document in the collection.
     * @param {Object} params - Parameters for creating the document.
     * @param {Object} params.data - Data to save in the new document.
     * @param {function} params.callBackSuccess - Callback function to call on successful creation.
     * @param {function} params.callBackError - Callback function to call on creation error.
     */
    async Create({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const newDocRef = doc(this.collectionRef);
            await setDoc(newDocRef, data);
            callBackSuccess(newDocRef.id);
        } catch (error) {
            callBackError(error);
        }
    }

    /**
     * Update an existing document in the collection by its ID.
     * @param {Object} params - Parameters for updating the document.
     * @param {string} params.id - ID of the document to update.
     * @param {Object} params.data - Data to update in the document.
     * @param {function} params.callBackSuccess - Callback function to call on successful update.
     * @param {function} params.callBackError - Callback function to call on update error.
     */
    async Update({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const docRef = doc(this.collectionRef, id);
            await updateDoc(docRef, data);
            callBackSuccess();
        } catch (error) {
            callBackError(error);
        }
    }

    /**
     * Delete a document from the collection by its ID.
     * @param {Object} params - Parameters for deleting the document.
     * @param {string} params.id - ID of the document to delete.
     * @param {function} params.callBackSuccess - Callback function to call on successful deletion.
     * @param {function} params.callBackError - Callback function to call on deletion error.
     */
    async Delete({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const docRef = doc(this.collectionRef, id);
            await deleteDoc(docRef);
            callBackSuccess();
        } catch (error) {
            callBackError(error);
        }
    }

    /**
     * Delete all documents from the collection.
     * @param {Object} params - Parameters for deleting all documents.
     * @param {function} params.callBackSuccess - Callback function to call on successful deletion.
     * @param {function} params.callBackError - Callback function to call on deletion error.
     */
    async DeleteAll({callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const querySnapshot = await getDocs(this.collectionRef);
            const batch = writeBatch(db);
            querySnapshot.forEach((doc) => {
                batch.delete(doc.ref);
            });
            await batch.commit();
            callBackSuccess();
        } catch (error) {
            callBackError(error);
        }
    }

    /**
     * Update all documents that match the query parameters.
     * @param {Object} params - Parameters for updating the documents.
     * @param {Object} params.data - Data to update in the documents.
     * @param {Array} [params.queryParams=[]] - Query parameters to filter the documents to update.
     * @param {function} params.callBackSuccess - Callback function to call on successful update.
     * @param {function} params.callBackError - Callback function to call on update error.
     */
    async UpdateAll({data, queryParams = [], callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            let q = query(this.collectionRef);
            if (queryParams.length > 0) {
                q = query(this.collectionRef, ...queryParams.map(param => where(param.field, param.operator, param.value)));
            }
            const querySnapshot = await getDocs(q);
            const batch = writeBatch(db);
            querySnapshot.forEach((doc) => {
                const docRef = doc.ref;
                batch.update(docRef, data);
            });
            await batch.commit();
            callBackSuccess();
        } catch (error) {
            callBackError(error);
        }
    }
}
