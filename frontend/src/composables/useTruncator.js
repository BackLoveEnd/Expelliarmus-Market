import {computed} from "vue";

export function useTruncator() {
    function truncateString(value, size) {
        return computed(() => {
            return value.length > size ? value.substring(0, size) + "..." : value;
        });
    }

    return {truncateString};
}
