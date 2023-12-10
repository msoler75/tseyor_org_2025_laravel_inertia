import { defineStore } from "pinia";

//export const usePlayer = createGlobalState(() => {
export const useFilesOperation = defineStore("files", {
  state: () => ({
    isMovingFiles: false,
    isCopyingFiles: false,
    filesToMove: [],
    filesToCopy: [],
  }),
});
