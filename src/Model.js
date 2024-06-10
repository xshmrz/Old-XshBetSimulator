import {initializeApp}                                                                                                           from 'firebase/app';
import {getFirestore, collection, getDocs, addDoc, doc, getDoc, updateDoc, deleteDoc, query, where, writeBatch, orderBy, setDoc} from 'firebase/firestore';
const firebaseConfig = {
    apiKey           : 'AIzaSyCzv6h36Y0QoDtAOIOyXVVwczeyTB3N-xQ',
    authDomain       : 'xsh-react-firebase.firebaseapp.com',
    databaseURL      : 'https://xsh-react-firebase-default-rtdb.europe-west1.firebasedatabase.app',
    projectId        : 'xsh-react-firebase',
    storageBucket    : 'xsh-react-firebase.appspot.com',
    messagingSenderId: '775499313241',
    appId            : '1:775499313241:web:8c43de230e03033ea94148'
};
const app            = initializeApp(firebaseConfig);
const db             = getFirestore(app);
export class Model {
    constructor(collectionName) {
        this.collectionRef = collection(db, collectionName);
    }
    async Get({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const docRef  = doc(this.collectionRef, id);
            const docSnap = await getDoc(docRef);
            if (docSnap.exists()) {
                callBackSuccess(docSnap.data());
            }
            else {
                callBackError('No such document!');
            }
        }
        catch (error) {
            callBackError(error);
        }
    }
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
            const results       = [];
            querySnapshot.forEach((doc) => {
                results.push({id: doc.id, ...doc.data()});
            });
            callBackSuccess(results);
        }
        catch (error) {
            callBackError(error);
        }
    }
    async Create({data, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const newDocRef = doc(this.collectionRef);
            await setDoc(newDocRef, data);
            callBackSuccess(newDocRef.id);
        }
        catch (error) {
            callBackError(error);
        }
    }
    async Update({id, data, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const docRef = doc(this.collectionRef, id);
            await updateDoc(docRef, data);
            callBackSuccess();
        }
        catch (error) {
            callBackError(error);
        }
    }
    async Delete({id, callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const docRef = doc(this.collectionRef, id);
            await deleteDoc(docRef);
            callBackSuccess();
        }
        catch (error) {
            callBackError(error);
        }
    }
    async DeleteAll({callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            const querySnapshot = await getDocs(this.collectionRef);
            const batch         = writeBatch(db);
            querySnapshot.forEach((doc) => {
                batch.delete(doc.ref);
            });
            await batch.commit();
            callBackSuccess();
        }
        catch (error) {
            callBackError(error);
        }
    }
    async UpdateAll({data, queryParams = [], callBackSuccess = () => {}, callBackError = () => {}}) {
        try {
            let q = query(this.collectionRef);
            if (queryParams.length > 0) {
                q = query(this.collectionRef, ...queryParams.map(param => where(param.field, param.operator, param.value)));
            }
            const querySnapshot = await getDocs(q);
            const batch         = writeBatch(db);
            querySnapshot.forEach((doc) => {
                const docRef = doc.ref;
                batch.update(docRef, data);
            });
            await batch.commit();
            callBackSuccess();
        }
        catch (error) {
            callBackError(error);
        }
    }
}







