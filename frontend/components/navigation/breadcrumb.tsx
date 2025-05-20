import React from "react";
import Link from "next/link";

export interface Crumb {
  label: string;
  href: string;
}

interface BreadcrumbProps {
  path: Crumb[];
}

export function Breadcrumb({ path }: BreadcrumbProps) {
  return (
    <nav
      className="hidden md:block text-sm text-gray-600"
      aria-label="Breadcrumb"
    >
      <ol className="list-none p-0 inline-flex space-x-1">
        {path.map((crumb, idx) => (
          <li key={idx} className="inline-flex items-center">
            {idx > 0 && <span className="mx-2 text-gray-400">/</span>}
            <Link href={crumb.href} className="hover:underline">
              {crumb.label}
            </Link>
          </li>
        ))}
      </ol>
    </nav>
  );
}
