import { defineStore } from "pinia";

export const useFilesStore = defineStore("files", {
  state: () => ({
    // file operations
    isMovingFiles: false,
    isCopyingFiles: false,
    filesToMove: [],
    filesToCopy: [],
  }),
  getters: {},
  actions: {},
});
