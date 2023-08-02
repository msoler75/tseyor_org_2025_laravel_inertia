import { createGlobalState, useStorage } from "@vueuse/core";

// file operations
export const useFilesOperation = createGlobalState(() =>
  useStorage("vue-use-locale-storage", {
    isMovingFiles: false,
    isCopyingFiles: false,
    filesToMove: [],
    filesToCopy: [],
  })
  );
