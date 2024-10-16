import { cva, type VariantProps } from "class-variance-authority";

export { default as Badge } from "./Badge.vue";

export const badgeVariants = cva(
  "inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2",
  {
    variants: {
      variant: {
        default:
          "border-transparent bg-primary text-primary-foreground hover:bg-primary/80",
        secondary:
          "border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80",
        destructive:
          "border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80",
        outline: "text-foreground",
        blue: "bg-blue-200/80 dark:bg-blue-800/80 text-blue-800 dark:text-blue-200",
        yellow:
          "bg-yellow-200/80 dark:bg-yellow-800/80 text-yellow-800 dark:text-yellow-200",
        violet:
          "bg-violet-200/80 dark:bg-violet-800/80 text-violet-800 dark:text-violet-200",
        rose: "bg-rose-200/80 dark:bg-rose-800/80 text-rose-800 dark:text-rose-200",
        emerald:
          "bg-emerald-200/80 dark:bg-emerald-800/80 text-emerald-800 dark:text-emerald-200",
        gray: "bg-gray-200/80 dark:bg-gray-700/80 text-gray-800 dark:text-gray-200",
        green:
          "bg-green-200/80 dark:bg-green-800/80 text-green-800 dark:text-green-200",
        red: "bg-red-200/80 dark:bg-red-800/80 text-red-800 dark:text-red-200",
        purple:
          "bg-purple-200/80 dark:bg-purple-800/80 text-purple-800 dark:text-purple-200",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
);

export type BadgeVariants = VariantProps<typeof badgeVariants>;
