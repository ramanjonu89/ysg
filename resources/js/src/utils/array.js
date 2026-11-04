export function mergeAndRemoveDuplicateTwoArrays (array1 , array2) {
  let collectionOfTwoArrays = [...array1, ...array2];
  return uniqueList(collectionOfTwoArrays);
}
const uniqueList = array => [...new Set(array)];
