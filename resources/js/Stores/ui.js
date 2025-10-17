import { reactive } from "vue";
import useFolderExplorerStore from "@/Stores/folderExplorer";
import useSelectors from "./selectors";
import usePlayer from "./player";
import useTools from "./tools";

// clase ui que agrupa elementos de la interfaz y métodos útiles

const folderExplorer = useFolderExplorerStore();
const selectors = useSelectors();
const player = usePlayer();
const tools = useTools();
const nav = useNav();

const ui = reactive({
    folderExplorer,
    selectors,
    player,
    tools,
    nav,
})


export default function useUi() { return ui }
