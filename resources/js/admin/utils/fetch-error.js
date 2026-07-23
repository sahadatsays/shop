export const extractErrorMessage = (payload, fallback = 'Something went wrong.') => {
    if (payload?.errors) {
        const messages = Object.values(payload.errors)
            .flat()
            .filter(Boolean);

        if (messages.length) {
            return messages.join(' ');
        }
    }

    if (payload?.message) {
        return payload.message;
    }

    return fallback;
};

export const readJsonResponse = async (response) => {
    try {
        return await response.json();
    } catch {
        return null;
    }
};
