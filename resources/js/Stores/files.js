const state = reactive({
  isMovingFiles: false,
  isCopyingFiles: false,
  filesToMove: [],
  filesToCopy: [],
});

//export const usePlayer = createGlobalState(() => {
export default function useFilesOperation() {
  return state;
}
