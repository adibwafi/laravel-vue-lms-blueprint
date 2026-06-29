import UAParser from "ua-parser-js";

const parser = new UAParser();

export const isMobile = () => {
  const result = parser.getResult();
  return result.device.type === "mobile";
};
