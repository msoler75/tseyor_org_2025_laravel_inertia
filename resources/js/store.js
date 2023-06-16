import {defineStore} from 'pinia';

export const useStore=defineStore('app',{
    state:()=> ({
        // file operations
        isMovingFiles: false,
        isCopyingFiles: false,
        filesToMove: [],
        filesToCopy: []
    }),
	getters:{
	},
	actions:{
	}
});
