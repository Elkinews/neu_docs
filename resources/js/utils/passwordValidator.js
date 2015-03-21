/* eslint-disable require-unicode-regexp */
const PASSWORD_RULES = [
	{
		message: 'Password must be at least 12 characters long',
		rule: /.{12,}/,
	},
	{
		message: 'Password must have a mix of uppercase and lowercase letters',
		// \p{Ll} and \p{Lu} equiv. of [[:lower:]] and [[:upper:]] (Unicode)
		rule: /\p{Ll}.*\p{Lu}|\p{Lu}.*\p{Ll}/u,
	},
	{
		message: 'Password must contain at least 2 digits',
		rule: /\d+.*\d+/,
	},
	{
		message: 'Password can not have a digit at the beginning or end',
		rule: /^\D+.*\D$/,
	},
	{
		message: 'Password must have at least one special character',
		// equivalent of [[:punct:]] (ASCII)
		rule: /[!-/:-@[-`{-~]/,
	},
];
/* eslint-enable require-unicode-regexp */
export const PASSWORD_GUIDE = [
    "Minimum length of twelve (12) alphanumeric characters.",
    "Password must contain a mix of upper and lower case characters.",
    "Password must have at least 2 numeric characters.",
    "Numeric characters must not be at the beginning or the end of Password.",
    'Password must include at least one special character from the following list: !@#$%^&*()_+-={}|[]~`:"'+";'<>?,./",
    "Password cannot have been used within the last four (4) years.11"
];


export function isValidPassword(value) {
	return validatePassword(value) === true;
}

export function validatePassword(value) {
	if (value.length === 0) {
		return 'Password is required';
	}

	for (const { message, rule } of PASSWORD_RULES) {
		if (!rule.test(value)) {
			return message;
		}
	}

	return true;
}

export function getRequiredRules() {
    let rules = {
        required: (value) => !!value || "Required.",
    }
    
    return rules.required;
}

export function getFormPasswordRules() {
    let rules = {
        charactersLong: (value) => PASSWORD_RULES[0].rule.test(value) || PASSWORD_RULES[0].message,
        upperAndLowerCase: (value) => PASSWORD_RULES[1].rule.test(value) || PASSWORD_RULES[1].message,
        twoDigits: (value) => PASSWORD_RULES[2].rule.test(value) || PASSWORD_RULES[2].message,
        digitOnStartOrEnd: (value) => PASSWORD_RULES[3].rule.test(value) || PASSWORD_RULES[3].message,
        oneSpecialCharacter: (value) => PASSWORD_RULES[4].rule.test(value) || PASSWORD_RULES[4].message,
        required: (value) => !!value || "Required.",
    }
    
    return [
        rules.charactersLong,
        rules.upperAndLowerCase,
        rules.twoDigits,
        rules.digitOnStartOrEnd,
        rules.oneSpecialCharacter,
        rules.required,
    ];
}

export function getPasswordMessageGuideList() {
    let messages = '<ul>';
    PASSWORD_GUIDE.forEach(message => {
        messages += '<li>' +  message + '</li>';
    });
    messages += '</ul>';

    return messages;
}
