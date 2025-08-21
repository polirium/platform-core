import "./src/autosize"
import "./src/countup"
import "./src/input-mask"
import "./src/dropdown"
import "./src/tooltip"
import "./src/popover"
import "./src/switch-icon"
import "./src/tab"
import "./src/toast"

import * as bootstrap from "bootstrap"
import * as tabler from "./src/tabler"

export {
	Alert,
	Modal,
	Toast,
	Tooltip,
	Tab,
	Button,
	Carousel,
	Collapse,
	Dropdown,
	Popover,
	ScrollSpy,
	Offcanvas
} from 'bootstrap'


//Polirium Core
import '../../../../../../vendor/power-components/livewire-powergrid/dist/powergrid'
import 'flatpickr'
import 'tom-select'
import 'autonumeric'

globalThis.bootstrap = bootstrap
globalThis.tabler = tabler
